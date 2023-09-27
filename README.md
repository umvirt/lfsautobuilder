# Umvirt LFS Auto Builder

ULFS Version: 0.2 

LFS Version: 12.0-systemd

License: GPL

## About

Automated Linux From Scratch (LFS) base image builder.

## Disclaimer

Data backup is needed before using this software.

This software is aimed to experienced Linux From Scratch maintainers.

Runing this software by unexperienced users can lead to data loss & hadware damage! 

## Building environment

There are few variants of building environment:

1.Physical computer.

1.a.Physical computer with local disk.

1.b.Physical computer with block device.

2.Virtual Machine with two drives.

### Pros & cons

**1.Physical computer**

Pros:

- maximum amount of available memory  

Cons:

- installed software can affect on building process.
- error can damage installed software & data.

**1.a.Physical computer with local disk**

Pros:

- faster than block device

Cons:

- additional drive is needed

**1.b.Physical computer with block device**

Pros:

- can be used free diskspace

Cons:

- issues with GRUB bootloader installation. GRUB Live CD is needed to boot & install bootloader manualy.

**2.virtual machine with two drives**

Pros:

- no additional software that can affect on building process
- absolutely safe
- no issues with grub

Cons:

- part of available memory is taken by host
- slower than physical computer

### Requirements

#### Hardware

**CPU:** x86_64 (amd64)


**HDD:** 30GB

**SWAP:** no

**RAM:** 2GB per core 

#### Software

**OS:** GNU/Linux. We prefere ULFS 0.1 (Linux From Scratch 8.3-systemd)

To check software run 

    bash version-check.sh

You have to get:

    OK:    Coreutils 8.30   >= 7.0
    OK:    Bash      4.4.18 >= 3.2
    OK:    Binutils  2.31.1 >= 2.13.1
    OK:    Bison     3.0.5  >= 2.7
    OK:    Diffutils 3.6    >= 2.8.1
    OK:    Findutils 4.6.0  >= 4.2.31
    OK:    Gawk      4.2.1  >= 4.0.1
    OK:    GCC       8.2.0  >= 5.1
    OK:    GCC (C++) 8.2.0  >= 5.1
    OK:    Grep      3.1    >= 2.5.1a
    OK:    Gzip      1.9    >= 1.3.12
    OK:    M4        1.4.18 >= 1.4.10
    OK:    Make      4.2.1  >= 4.0
    OK:    Patch     2.7.6  >= 2.5.4
    OK:    Perl      5.28.0 >= 5.8.8
    OK:    Python    3.7.0  >= 3.4
    OK:    Sed       4.5    >= 4.1.5
    OK:    Tar       1.30   >= 1.22
    OK:    Texinfo   6.5    >= 5.0
    OK:    Xz        5.2.4  >= 5.0.0
    OK:    Linux Kernel 5.2.9 >= 4.14
    OK:    Linux Kernel supports UNIX 98 PTY
    Aliases:
    OK:    awk  is GNU
    OK:    yacc is Bison
    OK:    sh   is Bash
    Compiler check:
    OK:    g++ works

In addition you also need followed software packages:

- **php** - to run dlist.php
- **qemu** - to run virtual machine and to create disk images (block devices)
- **mutipath-tools** - to mount partitions on block devices

### Resource allocation for VM

Because host consumes memory, Virtual Machine memory & CPUs are need to be reduced. 

For example, if you have 16GB of RAM, VM can have 12GB. 4GB should be reserved to host. 
Because one core consumes 2GB of RAM, maximum amount of cores is 6.

Lack of free host memory can cause Virtual Machine sudden poweroff. 
You can check kernel error messages with *dmesg* command 

### Estemated time amount (ETA)

Automatic building LFS without tests on virtual machine (6 CPUs, 12GB RAM) on AMD FX8350 is takes about 157 minutes.

To speed up building you can use

- more powerful CPU
- more faster storage: SSD, RAM-disk

## Disk image

### Disk layout

**Storage type:** RAW - Allows to mount block device.

**Partition table:** DOS - Support for old hardware & emulators.

**Only one partition:**

1. **root (sda1):** 30GB, ext4, bootable - reduce backup size by ignoring swap partition.

**Swap partition:** none - can be placed on additional disk.

### Final result

After building you will get:

- LFS book based instructions
- Kernel with default configuration
- HDD support: SATA
- Timezone: UTC
- Root password: none

This is not the end. It's just a beginning!


## Preparation

### Downloading

Download *wget-list* & *md5sums* files from LFS site and put in books directory

Run *downloadsrc* to get source code packages

    ./downoadsrc


### Integrity check

Run *checksrc* to perform integrity check

    ./checksrc

You will get:

    ------------------------------------
     Linux From Scratch Integrity Check 
    ------------------------------------

    Downloading check

    Files wanted: 94 
    Files found: 94 
    Files not found: 0 
    
    Checksum check
    
    Files with checksums: 89 
    Files with wrong checksums: 0 



### Disk image creating

To create disk image file you can use *qemu-img* command:

    qemu-img create -f raw hdd.img 30G

To create partition table & bootable ext4 patition you can use *fdisk* command:

    fdisk hdd.img

### Disk image partition formating 

*Note: This step is used when building performed on physical computer.
If you perform building in virtual machine skip this step.*

At first you have to pass disk image file to device mapper

    kpartx -av hdd.img

After passing file to device mapper you can access to partition.

Now you can format partition on disk image:

    mkfs.ext4 /dev/mapper/loop3p1

### Disk image partition mounting

*Note: This step is used when building performed on physical computer.
If you perform building in virtual machine skip this step.*

After formating you can mount partition:

    mount /dev/mapper/loop3p1 builddir


## Building

### Automatic build

Automatic build can be performed only in virtual machine.

**Warning: Running autmatic build on physical computer cause data loss!**

In order of autmatic build you have to run just one command:

    ./vmautobuild

You can place this command in time program to count build time:

    time ./vmautobuild

### Semiautomatic build

### Initial state

Before start you have:

- Source code downloaded & integrity is checked.
- Build environment meet requirements.
- Target partition mounted in builddir directory.
- You logined as *root* and located on root directory of this software.

### Stage 0

Copy source code to target partition

    ./copysrc

Create directories on target partition

    ./initdirs

Create *lfs* user on build environment

    ./adduser

Change directiories ownership on target partition to *lfs* user

    ./chown2user

### Stage 1

On this stage all operations performed by *lfs* user.
You can emulate *lfs* user activity by *root* or login as *lfs*

### Variant A. Running Stage 1 by root

If you want to make operations from root run the command

    su - lfs -c "cd `pwd` && time ./runscripts"

If everything is ok delete source code packages from target partition

    ./deletesrc

Now you can go to next stage

### Variant B. Running Stage 2 by lfs

Login to *lfs* shell

    su - lfs

Load shell configuration

    . userenv.sh

Now you can run scripts

    time ./runscripts

If everything is ok delete source code packages from target partition

    rm -rf builddir/sources

Logout from *lfs* shell

    exit

### Stage 2

Check that you *root*. If not login as *root*:

    whoami

Copy source code again to target partition. Old directories in sources directory broke new compilations:

    ./copysrc

Create directories for chrooting:

    ./chrootinit

Mount system filesystems on target partition:

    ./chrootmount

Run build operations in chroot environment:

    time ./runchrootscripts

If target partition is not placed on disk image install GRUB boot manager:

    ./chrootcmd grub-install /dev/sda

Delete *root* password. Make it empty:

    ./chrootcmd passwd -d root

Delete source code packages:

    ./deletesrc

Cleanup file system & zeroing free space:

    ./finish

Unmount system filesystems on target partition:

    ./chrootumount

Unmount target partition

    umount builddir

## Additional information

### Directory structure

- **books/** - files provided with LFS book.
    - wget-list - source packages & patches files list.
    - md5sums - md5-checksums for downloaded files integrity check.
- **builddir/** - LFS root partition mount point.
- **samples/** - configuration file samples.
- **cscripts/** - scripts to run by **root** user in chroot mode (Stage 2).
- **cscripts.config/** - config files for cscripts.
- **scripts** - scripts to run by **lfs** user (Stage 1).
- **src/** - downloaded source code packages.
- **tmux/** - tmux text user iterface (TUI) widgets
- **tmp/** - temporary directory.
    - dlist.txt - wanted source code & patches files. This file generated by *dlist.php* from URLs which stored in *books/wget-list* file. If you don't have php to generate this file, simply generate it on another machine and plece it here.
- adduser - add *lfs* user to host.
- checksrc - check source code files and print report.
- chrootcmd - run command in target's chroot environment.
- chrootinit - init target's chroot environment.
- chrootmount - mount system filesystems on target's chroot environment.
- chrootshell - run shell in target's chroot environment.
- chrootumount - unmount system filesystems from target's chroot environment.
- copysrc - copy source code files to target.
- deletesrc - delete source code files from target.
- dlist.php - wget-list parser. needed to get wanted files by checksrc.
- downloadsrc - downloads source code files and put them in src folder. Log is stored in tmp/wget.log.
- env.sh - config file for some scripts.
- finish - finish operations. */tmp* cleanup & free space zeroing.
- initdirs - create dirs in target.
- runchrootscripts - run all chroot scripts one by one (Stage 2).
- runscripts - run lfs scripts one by one (Stage 1).
- runscriptsbyroot.
- tmux-cscripts - run cscripts with tmux (experimental).
- userenv.sh - config file for lfs user.
- version-check.sh - check package versions.
- vmautobuild - one click target building **(DANGEROUS*)**
- vmcmds.txt - one click target building scenario.
- vmfinish - like finish but with root password deletion and grub bootloader installation.
- vmstart - formats /dev/sda1 partition WITHOUT PROMPT and mout it on mount point **(DANGEROUS*)** 

**(DANGEROUS)** - running this script cause /dev/sda1 formating therefore don't run this script on physical computer. 

Scripts with "vm" prefix is intended to run only in virtual machine. Be careful!




