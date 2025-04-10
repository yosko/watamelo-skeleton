# Watamelo Skeleton
A skelaton used to start a new project based on the Watamelo framework.

## How to use

Simplest way: create a new project based on this package:
```
composer create-project yosko/watamelo-skeleton skel-test dev-main \
  --repository='{"type": "git", "url": "git@github.com:yosko/watamelo-skeleton"}'
```

The app name (`skel-test`) will be used:
1. as the subdirectory for your code.
2. as the main (root) namespace for classes (`src/App.php` will be `\SkelTest\App`).

## Route callback

When declaring a route, it can be either a pair of:
- instance of a class and name of