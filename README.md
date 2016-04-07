# Timely

Simple PHP console tool to track your times

[![Latest Stable Version](https://img.shields.io/packagist/v/wick-ed/timely.svg?style=flat-square)](https://packagist.org/packages/wick-ed/timely) 
 [![Total Downloads](https://img.shields.io/packagist/dt/wick-ed/timely.svg?style=flat-square)](https://packagist.org/packages/wick-ed/timely)
 [![License](https://img.shields.io/packagist/l/wick-ed/timely.svg?style=flat-square)](https://packagist.org/packages/wick-ed/timely)
 [![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/wick-ed/timely/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/wick-ed/timely/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/wick-ed/timely/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/wick-ed/timely/?branch=master)

## Introduction



## Semantic versioning
This library follows semantic versioning and its public API defines as follows:

* The commands it exposes over the command line interface
* The format of its storage file

## Usage

As an alias like:
```bash
alias timely="php /<DIR_TO_TIMELY>/timely/bin/timely $*"
```

And use like:
```bash
timely track FOO-127 bar
```

# Commands

## track

### Description

### Syntax

```
timely track <TICKET> <COMMENT>
``

### Options

## show

### Description
Show tracked times

### Syntax

```
timely show [--ft] [<TICKET/PATTERN>]
``

### Options

`--f=<DATE>`

Show from a certain date on


`--t=<DATE>`

Shows a tracked tasks up to a certain date