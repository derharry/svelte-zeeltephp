#!/bin/bash
#
# post-build-ZeePHP.sh
# "build":     "vite build --mode build     && cp ./.env.build      ./buildx/.env  && rm -rf ./build                          && mv ./buildx ./build                             && echo 'finished ./build' ",
# "build.zp":  "vite build --mode build.zp  && cp ./.env.build.zp   ./buildx/.env  && rm -rf ../../wordpress_test/orangegids  && mv ./buildx ../../wordpress_test/oranjegids     && echo 'finished ./build-zp ?/wordpress_test/oranjegids'",
# "build.zp2": "vite build --mode build.zp2 && cp ./.env.build.zp2  ./buildx/.env  && rm -rf ./build-          mv ./buildx ./build-zeepressat_orangegids       && echo 'finished ./build-zp-prod'",
#
echo 
echo "ZeeltePHP post-build"
mode=$1
env_file=".env.$mode"

# Read the environment file
echo "  ✔ Reading $env_file"
if [ -f "$env_file" ]; then
    export $(grep -v '^#' "$env_file" | xargs)
else
    echo "Error: $env_file not found"
    exit 0
fi

# Check if BUILD_DIR is set
echo "  ✔ Checking BUILD_DIR"
if [ -z "$BUILD_DIR" ]; then
    echo "Error: BUILD_DIR is not set in $env_file"
    exit 0
fi


# copy PUBLIC_ and 
echo "  ✔ Copying environment variables to ./build/api/zeeltephp/api/.env"
grep "^PUBLIC_" $env_file > $BUILD_DIR/api/zeeltephp/.env 
grep "^ZEELTEPHP_" $env_file >> $BUILD_DIR/api/zeeltephp/.env 


# copy /src/lib/zplib to /build/api/lib/
echo "  ✔ Copying /src/lib/zplib/ to $BUILD/api/lib/"
cp -r src/lib/zplib/* $BUILD_DIR/api/lib/


# ZeeltePHP  +page.server.php
#    copy all /src/routes/.../+page.server.php to /build/api/routes/...
echo "  ✔ Copying +page.server.php files"
SRC_DIR="src/routes"
DEST_DIR="$BUILD_DIR/api/routes"
# Find all +page.server.php files and copy them to the destination
find "$SRC_DIR" -name "+page.server.php" | while read -r file; do
    # Get the relative path of the file
    rel_path="${file#$SRC_DIR/}"
    # Create the destination directory structure
    mkdir -p "$DEST_DIR/$(dirname "$rel_path")"
    # Copy the file
    cp "$file" "$DEST_DIR/${rel_path%/*}"
done
#echo "    Copied all +page.server.php files to $DEST_DIR"


# Execute the build steps  # this is done by SvelteKit-default-build now :-)
#rm -rf "$BUILD_DIR"
#mv ./build "$BUILD_DIR"
echo "  ✔ done "
echo 
