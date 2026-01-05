# SimpleDocs ToDo List

## Permissions/Permissions

- Refactor controller tests to follow new convention

## Permissions/Roles

- Refactor tests to remove parameters from toBe*() methods

- Refactor controller tests to follow new convention

## Disks/DiskTypes

- Add cache on `DiskType::getValidationRules()` to improve performance

- Add cache on `DiskType::getFormRepresentation()` to improve performance

## Disks/Disks

- Add action to validate disk config (should stablish connection)

- Validate that disk config is valid before create or update disk

- Validate `config` array content in `StoreDiskRequest`

- Validate `config` array content in `UpdateDiskRequest`

## Others

- Change server workflow to use `composer setup` to initialize project