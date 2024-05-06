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
        replace:
            -   metric: filesystem.file_count
                type: relative
                write: paths
                option: paths
                owners: [ '@Hulk', '@DrStrange' ]
```

## Options for single metric

| Option | Description                                                                                                                                                                                                                                           |
|--------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| metric | **(Required)** The unique naming of the metric were an option should be replaced.                                                                                                                                                                     | 
| option | **(Required)** The option in the given metric that should be replaced with the listing parsed from the code owners file.                                                                                                                              | 
| owners | **(Required)** A list of owners that should be searched for in the codeowners file. The results will be merged together and hand over to the metric option.                                                                                           | 
| type   | **(Optional)** can be `relative` or `absolute`. The paths will be given relative to the cwd path or absolute. In default the relative type is given.                                                                                                  |
| write  | **(Optional)** can be `files` or `paths`. It defines which of the owned types will be given. In default the option is set to `both` and so everything will be given - beware, because all paths are parsed for their files - so the list can be long. |

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
