# Panaly - Project Analyzer - CODEOWNERS Plugin

The plugin to the [Panaly Project Analyzer](https://github.com/DZunke/panaly) can be utilized to enable metrics which
are supporting paths receiving them from
a [CODEOWNERS](https://docs.github.com/en/repositories/managing-your-repositorys-settings-and-features/customizing-your-repository/about-code-owners)
file.

## Example Configuration

```yaml
# panaly.dist.yaml
plugins:
    DZunke\PanalyCodeOwners\CodeOwnersPlugin:
        codeowners: CODEOWNERS
        exclude_directories: [ 'vendor' ]
        replace:
            -   metric: filesystem.file_count
                type: relative
                write: paths
                option: paths
                owners: [ '@Hulk', '@DrStrange' ]

groups:
    ownership:
        title: "Information around the Project Ownership"
        metrics:
            unowned_directories: ~
            owned_files_count:
                owners: [ '@my_owner_group' ]
            owned_files_list:
                owners: [ '@my_owner_group' ]
            owned_directories_count:
                owners: [ '@my_owner_group', '@another_owner_group' ]
            owned_directories_list:
                owners: [ '@my_owner_group', '@another_owner_group' ]
```

## Options for single metric replacement

| Option | Description                                                                                                                                                                                                                                           |
|--------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| metric | **(Required)** The unique naming of the metric were an option should be replaced.                                                                                                                                                                     | 
| option | **(Required)** The option in the given metric that should be replaced with the listing parsed from the code owners file.                                                                                                                              | 
| owners | **(Required)** A list of owners that should be searched for in the codeowners file. The results will be merged together and hand over to the metric option.                                                                                           | 
| type   | **(Optional)** can be `relative` or `absolute`. The paths will be given relative to the cwd path or absolute. In default the relative type is given.                                                                                                  |
| write  | **(Optional)** can be `files` or `paths`. It defines which of the owned types will be given. In default the option is set to `both` and so everything will be given - beware, because all paths are parsed for their files - so the list can be long. |

## Available Metrics

**Unowned Directories**

The directory count with the name `unowned_directories` gives an `Table` result with a listing of all unowned directories.
Beware that the list can be very long. There are no options available. The CODEOWNER file from the plugin options is utilized.

**Owned Files Count**

The file count with the name `owned_files_count` gives an `IntegerValue` result with a summarization of all owned files 
of specific owners. The owners option has to be given to let the metric work correct, otherwise it will return a zero value.

| Option | Description                                                             |
|--------|-------------------------------------------------------------------------|
| owners | An array of owners that should be summarized to a single integer value. | 

**Owned Files Listing**

The file count with the name `owned_files_list` gives an `Table` result with a listing of all owned files with relative path
of specific owners. The owners option has to be given to let the metric work correct, otherwise it will return an empty list.

| Option | Description                                                             |
|--------|-------------------------------------------------------------------------|
| owners | An array of owners that should be summarized to a single integer value. | 

**Owned Directories Count**

The directory count with the name `owned_directories_count` gives an `IntegerValue` result with a summarization of all 
owned directories of specific owners. The owners option has to be given to let the metric work correct, otherwise it will 
return a zero value.

| Option | Description                                                             |
|--------|-------------------------------------------------------------------------|
| owners | An array of owners that should be summarized to a single integer value. | 

**Owned Directory Listing**

The file count with the name `owned_directories_list` gives an `Table` result with a listing of all owned directories 
with relative path of specific owners. The owners option has to be given to let the metric work correct, otherwise it 
will return an empty list.

| Option | Description                                                             |
|--------|-------------------------------------------------------------------------|
| owners | An array of owners that should be summarized to a single integer value. | 

## Known Problems

* There is no general specification for the `CODEOWNERS` file, so currently just
  the [Github specification](https://docs.github.com/en/repositories/managing-your-repositorys-settings-and-features/customizing-your-repository/about-code-owners)
  is supported
    * For example the [Gitlab specification](https://docs.gitlab.com/ee/user/project/codeowners/reference.html) with
      sections and section owners is not supported

## Thanks and License

**Panaly Project Analyzer - CODEOWNERS Plugin** © 2024+, Denis Zunke. Released utilizing
the [MIT License](https://mit-license.org/).

> GitHub [@dzunke](https://github.com/DZunke) &nbsp;&middot;&nbsp;
> Twitter [@DZunke](https://twitter.com/DZunke)
