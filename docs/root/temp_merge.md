# Merge Branch

合并分支

## merge-branch

- [Merge branch](https://github.com/marketplace/actions/merge-branch): Runs a git merge in your CI.

```yml
# Merge the parent template to the current template

name: Merge Template to Current
on:
  push:
    branches:
      - "parent-master"

jobs:
  sync-branch:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@master

      - name: Merge parent -> master
        uses: devmasx/merge-branch@master
        with:
          type: now
          from_branch: "parent-master"
          target_branch: "master"
          github_token: ${{ github.ACCESS_TOKEN }}

      - name: Merge parent -> develop
        uses: devmasx/merge-branch@master
        with:
          type: now
          from_branch: "parent-master"
          target_branch: "develop"
          github_token: ${{ github.ACCESS_TOKEN }}
```

```mermaid
flowchart LR

Current["当前仓库父模板分支(parent-master)"]
Develop["开发分支(develop)"]
Master["主分支(master)"]

Current-->Develop
Current-->Master
```
