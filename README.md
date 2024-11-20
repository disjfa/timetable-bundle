# Timetable bundle

This is a test

```
composer config repositories.timetable-bundle vcs https://github.com/disjfa/timetable-bundle
composer req disjfa/timetable-bundle:dev-master --prefer-source
```

config/routes/disjfa_timetable.yaml
```yaml
disjfa_timetable:
    resource: '@DisjfaTimetableBundle/Controller/'
```
