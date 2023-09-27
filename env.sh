PWD=`pwd`
LFS="$PWD/builddir"

echo $LFS

MAKEFLAGS='-j8'

export PWD LFS MAKEFLAGS
