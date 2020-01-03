# Timely

Simple PHP console tool to track your times and push them into a remote time-keeping tool such as Jira.

![Travis (.org) branch](https://img.shields.io/travis/wick-ed/timely/master.svg?style=flat-square)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/wick-ed/timely/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/wick-ed/timely/?branch=master)
 [![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/wick-ed/timely/master.svg?style=flat-square)](https://scrutinizer-ci.com/g/wick-ed/timely/?branch=master)

## Introduction



## Semantic versioning
This tool follows semantic versioning and its public API defines as follows:

* The commands it exposes over the command line interface
* The format of its storage file
* The format of its configuration file and the given configuration options

## Usage

As an alias like:
```bash
alias timely="php /<DIR_TO_TIMELY>/timely/bin/timely $*"
```

And use like:
```bash
timely track FOO-127 bar
```

## Commands

There are several simple commands available, to track your everyday work:

* [track](#track)
* [show](#show)
* [pause](#pause)
* [push](#push)

### track

#### NAME

The **show** command tracks an activity you just started
This activity has to be in relation to an identifier such as a ticket ID from your ticket system.
Although this is only to structure your tracked times, it will be used when interaction with an actual ticket system.

An example call would be:

  timely **show** FOO-42 Doing some stuff now


**track** -- Track tickets you are starting to work on just now

#### SYNOPSIS

timely **track** ticket-id comment

#### DESCRIPTION

The **track** command tracks an activity you just started
This activity has to be in relation to an identifier such as a ticket ID from your ticket system.
Although this is only to structure your tracked times, it will be used when interaction with an actual ticket system.

#### EXAMPLES

Start tracking your work on ticket `FOO-42` leaving a comment
```
timely track FOO-42 Doing some stuff now
```

### show

#### NAME

**show** -- Show tracked tasks, filterable by ticket id

#### SYNOPSIS

timely **show** \[yesterday|today|current\] \[-t|f|s\] \[--to|from|specific\] \[ticket-id\]

#### DESCRIPTION

The **show** command is used to display times you have already tracked.
By default, these times are grouped by the (ticket) identifier you used for tracking.
Example output would look like this:

```
FOO-42     2019-11-28 17:41:17 -> 2019-11-29 15:59:25
====================================================
    * | 2019-11-28 17:41:17 | FOO-42 | 1h 15m | Doing some stuff now
    -------------------------------------------------
    1h 15m
```

Only tracked activities that already last or lasted longer than 15 minutes are shown.

#### EXAMPLES

The tracked times in question can also be filter by supplying the optional (ticket) identifier:
```
timely show FOO-42
```
You can further filter by narrowing the time interval.
This can be done by using supplied filter keywords current, today or yesterday:
```
timely show current
```
or
```
timely show yesterday
```

This filters for the tracking currently active (as in "what you are currently doing") or all tracked times of yesterday.

Filtering the processed time trackings is also possible through the **from**, **to** and **specific** options.
These options support the PHp date and time format as described here: https://www.php.net/manual/en/datetime.formats.php
This allows for refined filtering as shown in the examples below.

Filter for a certain specific date:
```
timely show -s 2019-11-28
```

Filter for a given time range:
```
timely show -f 2019-11-28 -t 2019-11-30
```

Filter for the last week:
```
timely show -f"-1 week"
```

Filter for everything within the last October:
```
timely show -f"first day of october" -t"last day of october"
```

### pause

#### NAME

**pause** -- Pause the current tracking until explicitly resumed

#### SYNOPSIS

timely **pause** \[-r|--resume|comment\]

#### DESCRIPTION

The **pause** command allows to pause the tracking of your current task.
This makes sense e.g. for a small break, lunch or simply for leaving work to continue the next morning.
```
timely pause going for lunch
```

After the pause is over, the current tracking must be resumed:
```
timely pause -r
```

If you start with something else, using the **track** command during an ongoing pause will also end the pause automatically.

#### EXAMPLES

See above.

### push

#### NAME

**push** -- Pushes booked times against the configured remote

#### SYNOPSIS

timely **push** \[yesterday|today|current\] \[-t|f|s\] \[--to|from|specific\] \[ticket-id\]

#### DESCRIPTION

The **push** command is used to push tracked times to an external time keeping service.
Jira being an example of a supported service.
Using the push command requires configuration of the service's endpoint and possibly authentication within the `.env` configuration file.

The command has the same syntax and usability as the **show** command.
On execution the command will use the service's internal format to process all tracked times that a similar **show** command would have displayed.

The following command would create e.g. Jira worklogs for yesterday's tasks:
```
timely push yesterday
```

The **push** command keeps track of already pushed time trackings so nothing gets pushed twice.

#### EXAMPLES

The tracked times in question can also be filter by supplying the optional (ticket) identifier:
```
timely push FOO-42
```
You can further filter by narrowing the time interval.
This can be done by using supplied filter keywords current, today or yesterday:
```
timely push current
```
or
```
timely push yesterday
```

This filters for the tracking currently active (as in "what you are currently doing") or all tracked times of yesterday.

Filtering the processed time trackings is also possible through the **from**, **to** and **specific** options.
These options support the PHp date and time format as described here: https://www.php.net/manual/en/datetime.formats.php
This allows for refined filtering as shown in the examples below.

Filter for a certain specific date:
```
timely push -s 2019-11-28
```

Filter for a given time range:
```
timely push -f 2019-11-28 -t 2019-11-30
```

Filter for the last week:
```
timely push -f"-1 week"
```

Filter for everything within the last October:
```
timely push -f"first day of october" -t"last day of october"
```

## Storage format

By default tracking data is stored within a simple text file called `timely-log.txt` within the `data` directory.
Storage was intentionally kept this simple so manipulating your trackings could be done with text editors only.
This allows for maximal flexibility, but lacks some of the more sophisticated features a database would give us.
Trackings are saved chronologically with the most recent tracking on top.
Also to allow for easy manual manipulation.

Working with the storage file always requires knowledge of the format in which trackings are saved.

Trackings are based on the following synopsis:

```
<date and time a tracking occured> |<identifier e.g. a ticket number> | <comment> | [<push status for remote services>];
```

An example tracking looks like this:
```
2019-11-28 17:41:17 | FOO-42 | Doing some stuff now | ;
```

An example tracking which was already pushed to a remote time-keeping service (Jira in this example) looks like this:
```
2019-11-28 17:41:17 | FOO-42 | Doing some stuff now | jira 2019-11-28 19:27:17;
```

This contains the name of the service and a timestamp of the actual push.

Pauses utilized by the **pause** command have the same format, but use a specific identifier so they can be identified as meta information.
An example of a pause (e.g. for a lunch break) containing start and end looks like this:
```
2019-11-28 12:41:17 | --pe-- |  | ;
2019-11-28 12:00:17 | --ps-- | lunch | ;
```
