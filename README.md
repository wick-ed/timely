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

There are several simple commands available, to track your everyday work:

* [track](#track)
* [show](#show)
* [pause](#pause)

## track

#### NAME

**track** -- Track tickets you are starting to work on just now

#### SYNOPSIS

timely **track** ticket-id comment

#### DESCRIPTION

Using **track** one can track the start of work on a certain ticket or issue. This will result in a timestamp indicating the start of time tracking for a certain ticket related task.
**track** is a standalone command in itself. Every tracked activity will automatically end with the next execution of the **track** command.

#### EXAMPLES

```shell
timely track SOMEPROJECT-42 Starting to work on issue 42
```

## show

#### NAME

**show** -- Show tracked tasks, filterable by ticket id

#### SYNOPSIS

timely **show** \[yesterday|today|current\] \[-ft\] \[ticket-id\]

#### DESCRIPTION

The **show** command is used to show all, or only a certain sub-portion, of tracked tasks.

| Command      | Parameter  | Description                                                           |
| -------------| -----------| ----------------------------------------------------------------------|
| `-t|--to`    | Date       | A date up to which tasks should be shown. Format should be Y-m-d      |
| `-f|--from`  | Date       | A date from which on tasks should be shown. Format should be Y-m-d    |

#### EXAMPLES

Show all tracked tasks related to the ticket with the ID `SOMEPROJECT-42`:
```shell
timely show SOMEPROJECT-42
```

Show all tracked tasks filtered by a pattern
```shell
timely show SOMEPROJECT*
```

Show all tasks which where tracked yesterday:
```shell
timely show yesterday
```

Show all tracked tasks from 24. January 2016 to 31. January 2016:
```shell
timely show -f 2016-01-24 -t 2016-01-31
```

## pause

#### NAME

**pause** -- Pause the current tracking until explicitly resumed

#### SYNOPSIS

timely **pause** \[-r|comment\]

#### DESCRIPTION

Allows to pause a currently tracked task. Will pause time tracking until explicitly resumed, again with the **pause** command.

| Command       | Description                                 |
| --------------| --------------------------------------------|
| `-r|--resume` | Resumes a previously paused task            |

#### EXAMPLES

Pause a currently tracked task:
```shell
timely pause Going for lunch
```

Resume a previously paused task
```shell
timely pause -r
```