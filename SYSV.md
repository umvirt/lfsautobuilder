# Sysv support research (draft)

Sysv init system support is dropped in LFS 13.0:


**The prior System V version of LFS is no longer maintained.**

Source: [https://www.linuxfromscratch.org/lfs/read.html](https://www.linuxfromscratch.org/lfs/read.html)

Sysv allows to create environmets with lower memory consumption.

Sysv can be very useful on retro, embeded deices and on virtual machines.

LFS autobuilder is continue Sysv support. 

A small sysv edition research was completed in order to understand it.

This document is describes research which allows to create sysv installation scripts.

We hope this document will be useful in future updates.

## Review LFS 12.4

LFS 12.4 sysv edition can be created from systemd edition.

Basic root scripts are same.

Differences in chroot scripts:

     08.74-markupsafe
     08.75-jinja2
     08.76-systemd
    -08.77-dbus
    -08.78-man-db
    -08.79-procps-ng
    -08.80-util-linux
    -08.81-e2fsprogs
    -08.83-stripping
    -08.84-cleanup
    +08.77-man-db
    +08.78-procps-ng
    +08.79-util-linux
    +08.80-e2fsprogs
    +08.81-sysklogd
    +08.82-sysvinit
    +08.84-stripping
    +08.85-cleanup
    +09.2-lfs-bootscripts
    +09.6-sysvinit
     10.2-fstab
     10.3-linux
     10.4-grub

## LFS 13.0 numeration shift

In 13.0 we have shift in scripts

     08.76-markupsafe
     08.77-jinja2
     08.78-systemd
    -08.79-dbus
    -08.80-man-db
    -08.81-procps-ng
    -08.82-util-linux
    -08.83-e2fsprogs
    -08.85-stripping
    -08.86-cleanup
    +08.79-man-db
    +08.80-procps-ng
    +08.81-util-linux
    +08.82-e2fsprogs
    +08.83-sysklogd
    +08.84-sysvinit
    +08.85-stripping
    +08.86-cleanup
    +09.2-lfs-bootscripts
    +09.6-sysvinit
     10.2-fstab
     10.3-linux
     10.4-grub

## Modified files

Same files which contents has been modified:

    07.06-files
    08.76-systemd
    10.2-fstab
    10.3-linux
    10.4-grub

Let's review DIFFerences:

#### 07.06-files

- Deleted all lines which started with 'systemd' from /etc/passwd
- Deleted all lines which started with 'systemd' from /etc/group

#### 08.76-systemd

- Before makedir

        sed -i '/systemd-sysctl/s/^/#/' rules.d/99-systemd.rules.in

        sed -e '/NETWORK_DIRS/s/systemd/udev/' \
            -i src/libsystemd/sd-network/network-util.h

- Overwrite meson setup

        meson setup ..                  \
              --prefix=/usr             \
              --buildtype=release       \
              -D mode=release           \
              -D dev-kvm-mode=0660      \
              -D link-udev-shared=false \
              -D logind=false           \
              -D vconsole=false

- Overwrite 'ninja' & 'ninja install'

        export udev_helpers=$(grep "'name' :" ../src/udev/meson.build | \
        awk '{print $3}' | tr -d ",'" | grep -v 'udevadm')


        ninja udevadm systemd-hwdb                                           \
              $(ninja -n | grep -Eo '(src/(lib)?udev|rules.d|hwdb.d)/[^ ]*') \
              $(realpath libudev.so --relative-to .)                         \
              $udev_helpers

        install -vm755 -d {/usr/lib,/etc}/udev/{hwdb.d,rules.d,network}
        install -vm755 -d /usr/{lib,share}/pkgconfig
        install -vm755 udevadm                             /usr/bin/
        install -vm755 systemd-hwdb                        /usr/bin/udev-hwdb
        ln      -svfn  ../bin/udevadm                      /usr/sbin/udevd
        cp      -av    libudev.so{,*[0-9]}                 /usr/lib/
        install -vm644 ../src/libudev/libudev.h            /usr/include/
        install -vm644 src/libudev/*.pc                    /usr/lib/pkgconfig/
        install -vm644 src/udev/*.pc                       /usr/share/pkgconfig/
        install -vm644 ../src/udev/udev.conf               /etc/udev/
        install -vm644 rules.d/* ../rules.d/README         /usr/lib/udev/rules.d/
        install -vm644 $(find ../rules.d/*.rules \
                              -not -name '*power-switch*') /usr/lib/udev/rules.d/
        install -vm644 hwdb.d/*  ../hwdb.d/{*.hwdb,README} /usr/lib/udev/hwdb.d/
        install -vm755 $udev_helpers                       /usr/lib/udev
        install -vm644 ../network/99-default.link          /usr/lib/udev/network

        tar -xvf ../../udev-lfs-20230818.tar.xz
        make -f udev-lfs-20230818/Makefile.lfs install

        tar -xf ../../systemd-man-pages-257.8.tar.xz                            \
            --no-same-owner --strip-components=1                              \
            -C /usr/share/man --wildcards '*/udev*' '*/libudev*'              \
                                          '*/systemd.link.5'                  \
                                          '*/systemd-'{hwdb,udevd.service}.8

        sed 's|systemd/network|udev/network|'                                 \
            /usr/share/man/man5/systemd.link.5                                \
          > /usr/share/man/man5/udev.link.5

        sed 's/systemd\(\\\?-\)/udev\1/' /usr/share/man/man8/systemd-hwdb.8   \
                                       > /usr/share/man/man8/udev-hwdb.8

        sed 's|lib.*udevd|sbin/udevd|'                                        \
            /usr/share/man/man8/systemd-udevd.service.8                       \
          > /usr/share/man/man8/udevd.8


- Replace lines

        -systemd-machine-id-setup
        +rm /usr/share/man/man*/systemd*

        -systemctl preset-all
        +unset udev_helpers

        +udev-hwdb update

#### 10.2-fstab 

This file replaced entirely. Because mount points are different 

#### 10.3-linux

Changes in 12.4:

    -cp -iv arch/x86/boot/bzImage /boot/vmlinuz-6.16.1-lfs-12.4-systemd
    +cp -iv arch/x86/boot/bzImage /boot/vmlinuz-6.16.1-lfs-12.4

#### 10.4-grub

Changes in 12.4:

    -menuentry "GNU/Linux, Linux 6.16.1-lfs-12.4-systemd" {
    -        linux   /boot/vmlinuz-6.16.1-lfs-12.4-systemd root=/dev/sda1 ro
    +menuentry "GNU/Linux, Linux 6.16.1-lfs-12.4" {
    +        linux   /boot/vmlinuz-6.16.1-lfs-12.4 root=/dev/sda1 ro

#### Sysv specific packages 

This packages versions are same - skiping them.

#### 08.81-util-linux 08.82-e2fsprogs

This packages are copied from 12.4-sysv systemd because contain specific instructions.

## Conclusion

It's possible to provide Sysv support if previous release used as sample.