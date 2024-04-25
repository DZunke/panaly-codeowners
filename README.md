# Panaly - Project Analyzer - CODEOWNERS Plugin

The plugin to the [Panaly Project Analyzer](https://github.com/DZunke/panaly) can be utilized to enable metrics which
are supporting paths receiving them from a [CODEOWNERS](https://docs.github.com/en/repositories/managing-your-repositorys-settings-and-features/customizing-your-repository/about-code-owners)
file. 

## Example Configuration

```yaml
# panaly.dist.yaml
plugins:
    DZunke\PanalyCodeOwners\CodeOwnersPlugin:
        codeowners: CODEOWNERS
        replace:
            -
                metric: filesystem.file_count
                type: relative
                option: paths
                owners: ['@Hulk', '@DrStrange']
```

## Known Problems

* There is no general specification for the `CODEOWNERS` file, so currently just the [Github specification](https://docs.github.com/en/repositories/managing-your-repositorys-settings-and-features/customizing-your-repository/about-code-owners) is supported
  * For example the [Gitlab specification](https://docs.gitlab.com/ee/user/project/codeowners/reference.html) with sections and section owners is not supported

## Thanks and License

**Panaly Project Analyzer - CODEOWNERS Plugin** © 2024+, Denis Zunke. Released utilizing
the [MIT License](https://mit-license.org/).

> GitHub [@dzunke](https://github.com/DZunke) &nbsp;&middot;&nbsp;
> Twitter [@DZunke](https://twitter.com/DZunke)
