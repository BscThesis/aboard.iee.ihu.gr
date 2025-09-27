#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR=$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")/.." && pwd)
BUILD_DIR="$ROOT_DIR/react_client/build"
TARGET_DIR="$ROOT_DIR/public"

pushd "$ROOT_DIR/react_client" >/dev/null
npm run build
popd >/dev/null

if [ ! -d "$BUILD_DIR" ]; then
  echo "Build directory not found: $BUILD_DIR" >&2
  exit 1
fi

rm -rf "$TARGET_DIR/static"
mkdir -p "$TARGET_DIR"
rsync -a --delete "$BUILD_DIR/static" "$TARGET_DIR/"
for file in asset-manifest.json favicon.ico index.html logo192.png logo512.png manifest.json robots.txt; do
  if [ -f "$BUILD_DIR/$file" ]; then
    cp "$BUILD_DIR/$file" "$TARGET_DIR/$file"
  fi
done

echo "React build copied to $TARGET_DIR"
