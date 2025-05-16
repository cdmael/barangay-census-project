# ProjectDatabaseIM
Barangay Litlit Census Web Application (Database - IM)

# What to do:
- download and set-up Git
- check "Enable Git" in VSCode
- in VSCode:
  - click "View"
  - open "Command Palette"
  - select "Git: Clone"
  - paste: https://github.com/kyrstnxx/ProjectDatabaseIM.git
  - generate new folder
  - open in VSCode

# Feature Branch 
- make sure youre starting from the latest code:
  - git checkout main
  - git pull origin main

- create your feature branch:
  - git checkout -b feature/my-feature
  - *do the coding and shiz

- you can now commit your changes:
  - git add .
  - git commit -m "describe what are your changes"

# !GOOD PRACTICE - REBASING ONTO MAIN
- good practice, especially if simultaneous 'yung nag-uupdate sa main branch:
  - git fetch origin
  - git rebase origin/main

- if magkaroon conflict, Git will pause. After resolving, run:
  - git add .
  - git rebase --continue

# MERGE TO MAIN (IF TESTED ALREADY)
- once feature is working and tested:
  - git checkout main
  - git pull origin
  - git merge feature/my-feature

# DELETE FEATURE BRANCH (OPTIONAL)
  - git branch -d feature/my-feature
