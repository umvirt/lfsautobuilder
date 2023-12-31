#PWD=`pwd`
#LFS="$PWD/builddir"

#echo $LFS

. env.sh

set +h
umask 022
LC_ALL=POSIX
LFS_TGT=i686-lfs-linux-gnu
PATH=/usr/bin
if [ ! -L /bin ]; then PATH=/bin:$PATH; fi
PATH=$LFS/tools/bin:$PATH
CONFIG_SITE=$LFS/usr/share/config.site
MAKEFLAGS=-j6
export LFS LC_ALL LFS_TGT PATH CONFIG_SITE
