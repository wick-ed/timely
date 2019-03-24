# Timely

Simple PHP console tool to track your times

![Travis (.org) branch](https://img.shields.io/travis/wick-ed/timely/master.svg?style=flat-square)
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

Start tracking your work on ticket `SOMEPROJECT-42` leaving a comment
```
timely track SOMEPROJECT-42 Starting to work on issue 42
```

## show

#### NAME

**show** -- Show tracked tasks, filterable by ticket id

#### SYNOPSIS

timely **show** \[yesterday|today|current|recent\] \[--tofrom\] \[ticket-id\]

#### DESCRIPTION

The **show** command is used to show all, or only a certain sub-portion, of tracked tasks.

| Command      | Parameter  | Description                                                           |
| -------------| -----------| ----------------------------------------------------------------------|
| `--to`       | Date       | A date up to which tasks should be shown. Format should be Y-m-d      |
| `--from`     | Date       | A date from which on tasks should be shown. Format should be Y-m-d    |

The **show** command also accepts several keywords used for specific filtering of its output.

| Keyword     | Description                                        |
| ------------| ---------------------------------------------------|
| `yesterday` | Will show all of yesterday's tracked tasks         |
| `today`     | Will show all of today's tracked tasks             |
| `current`   | Shows the task you are currently working on        |
| `recent`    | Shows the three most recent                        |

#### EXAMPLES

Show all tracked tasks related to the ticket with the ID `SOMEPROJECT-42`:
```
timely show SOMEPROJECT-42
```

Show all tracked tasks filtered by a pattern
```
timely show SOMEPROJECT*
```

Show all tasks which where tracked yesterday:
```
timely show yesterday
```

Show all tracked tasks from 24. January 2016 to 31. January 2016:
```
timely show --from 2016-01-24 --to 2016-01-31
```

## pause

#### NAME

**pause** -- Pause the current tracking until explicitly resumed

#### SYNOPSIS

timely **pause** \[--resume|comment\]

#### DESCRIPTION

Allows to pause a currently tracked task. Will pause time tracking until explicitly resumed, again with the **pause** command.

| Command    | Description                                 |
| -----------| --------------------------------------------|
| `--resume` | Resumes a previously paused task            |

#### EXAMPLES

Pause a currently tracked task:
```
timely pause Going for lunch
```

Resume a previously paused task
```
timely pause --resume
```
