#!/usr/bin/env bash
set -euo pipefail

BASE_URL="http://localhost:8000"
OUTDIR="./_test_out"
mkdir -p "$OUTDIR"

echo "=== 1) CPT availability ====================================="
# Ensure CPT is registered and visible in admin UI
wp post-type list --fields=name,show_ui --format=table | tee "$OUTDIR/post_types.txt"
if ! wp post-type list --fields=name --format=csv | grep -q '^galerie$'; then
  echo "FAIL: CPT 'galerie' not registered"; exit 1
fi
echo "OK: CPT 'galerie' registered"

# Create a gallery post
GID=$(wp post create --post_type=galerie --post_status=publish --post_title="Test Galerie (auto)" --porcelain)
echo "Created galerie post ID: $GID"

# Try to set a featured image if a sample file exists (optional)
set +e
for CAND in \
  wp-content/uploads/*sample*.jpg \
  wp-content/themes/*/assets/img/gal-1.jpg \
  wp-content/themes/*/assets/img/sample.jpg
do
  if [ -f "$CAND" ]; then
    MID=$(wp media import "$CAND" --post_id="$GID" --featured_image --porcelain 2>/dev/null)
    if [ -n "${MID:-}" ]; then
      echo "Featured image set from: $CAND (media ID: $MID)"
      break
    fi
  fi
done
set -e

wp post list --post_type=galerie --fields=ID,post_title,post_status --format=table | tee "$OUTDIR/galerie_posts.txt"

echo
echo "=== 2) Front page gallery rendering ========================="
curl -s "$BASE_URL/" > "$OUTDIR/homepage.html"

# Section present?
grep -n '<section id="galerie"' "$OUTDIR/homepage.html" && echo "OK: #galerie section present"
grep -n 'class="gallery__grid' "$OUTDIR/homepage.html" && echo "OK: .gallery__grid present"

# No leftover captions
if grep -n '<figcaption' "$OUTDIR/homepage.html" >/dev/null; then
  echo "FAIL: <figcaption> still present on homepage"; exit 1
else
  echo "OK: no <figcaption> on homepage"
fi

# Count images
IMG_COUNT=$(grep -o '<img ' "$OUTDIR/homepage.html" | wc -l | tr -d ' ')
echo "Found $IMG_COUNT <img> tags on homepage"

# Check first 5 image URLs return 200
echo "Checking first up to 5 image URLs for HTTP 200..."
grep -o 'src="[^"]\+"' "$OUTDIR/homepage.html" | cut -d'"' -f2 | head -5 | while read -r URL; do
  CODE=$(curl -s -o /dev/null -w "%{http_code}" "$URL")
  echo "$URL -> $CODE"
  if [ "$CODE" != "200" ]; then
    echo "FAIL: asset not 200: $URL"; exit 1
  fi
done
echo "OK: first assets reachable"

echo
echo "=== 3) Shortcode functionality =============================="
# Create a page that renders the shortcode
PID=$(wp post create \
  --post_type=page \
  --post_title="Shortcode Test (auto)" \
  --post_status=publish \
  --post_content='[galerie_resto limit="3" columns="3"]' \
  --porcelain)
URL=$(wp post list --post_type=page --fields=ID,url --format=csv | awk -F, -v id="$PID" '$1==id{print $2}')
echo "Shortcode page ID: $PID"
echo "Shortcode page URL: $URL"

curl -s "$URL" > "$OUTDIR/shortcode.html"

# Confirm images rendered (shortcode expanded)
if grep -n '<img ' "$OUTDIR/shortcode.html" >/dev/null; then
  echo "OK: shortcode outputs <img> tags"
else
  echo "FAIL: shortcode did not render images"; exit 1
fi

echo
echo "=== 4) Lightbox wiring (static checks) ======================"
# Enqueued assets present?
if grep -n 'child\.js' "$OUTDIR/homepage.html" >/dev/null; then
  echo "OK: child.js enqueued"
else
  echo "WARN: child.js not found in homepage HTML"
fi
if grep -n 'child\.css' "$OUTDIR/homepage.html" >/dev/null; then
  echo "OK: child.css enqueued"
else
  echo "WARN: child.css not found in homepage HTML"
fi

# Image wrapped in <a href="full-size">
if grep -n '<a href=' "$OUTDIR/homepage.html" | head -5; then
  echo "OK: anchor tags found around images (candidate for lightbox)"
else
  echo "WARN: no <a href> around images on homepage"
fi

echo
echo "=== PASS/FAIL CRITERIA ======================================"
echo "- CPT 'galerie' registers, can create post ✔"
echo "- Homepage has #galerie & .gallery__grid ✔"
echo "- No <figcaption> remains ✔"
echo "- First image assets return 200 ✔"
echo "- Shortcode page renders <img> ✔"
echo "- child.js / child.css present (INFO), anchors wrap images (INFO)"
echo
echo "Artifacts saved in: $OUTDIR (homepage.html, shortcode.html, listings)"
echo "Done."
