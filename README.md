# Usage

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
timely show [-scf] [<TICKET/PATTERN>]
``

### Options

```
-s
```
Sort by ticket

```
-c
```
List in chronologic order

```
-f
```
Show from a certain date on

```
-t
```
Show up to a certain date