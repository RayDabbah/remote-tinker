# remote-tinker

Run a local PHP file inside `php artisan tinker` on a configured remote Laravel server.

## Install

```bash
composer global require raydabbah/remote-tinker
```

## Usage

```bash
remote-tinker               # interactive runner
remote-tinker setup         # add / update / delete a remote
remote-tinker help          # show flags and commands
```

The runner SCPs your local file to the remote, runs it via `php artisan tinker --execute='require("...")'`, streams the output back, and removes the file from the remote.

## Returning files from the remote

Scripts that produce a file (an export, a report, a generated config) can have that file pulled back to your local machine automatically.

**On the remote side**, write to the directory in `REMOTE_TINKER_OUTPUT_DIR`:

```php
$dir = getenv('REMOTE_TINKER_OUTPUT_DIR') ?: sys_get_temp_dir();
file_put_contents($dir . '/report.xlsx', $xlsxBytes);
```

**On the local side**, file-return is opt-in. There are three ways to enable it (highest priority first):

1. **CLI flag** — `remote-tinker --return-files` (or `--no-return-files` to suppress even if configured on).
2. **Per-remote config** — answer "yes" to *Always pull output files back from this remote?* in `remote-tinker setup`. Stored as `alwaysReturnFiles` in the config.
3. **Interactive prompt** — with no flag and no config, the runner asks each run.

Pulled files land in the current working directory by default. Set `localOutputDir` on a remote in `~/.config/remote-tinker/remote-tinker.json` to override.

If `REMOTE_TINKER_OUTPUT_DIR` is unset on the remote (file-return wasn't enabled this run), your script can fall back to a temp dir or skip writing — `getenv()` will return `false`.
