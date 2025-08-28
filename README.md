<<<<<<< HEAD
# Barangay Census Project
Barangay Litlit Census Web Application (Database - IM)

DOCUMENTATION:
https://docs.google.com/document/d/1ltCMtKKxMeAtcRD9nEqw9a8om6XTNmYXDFQFYNCBpIo/edit?usp=drivesdk

PROGRESS: https://docs.google.com/document/d/1rrKZpe0anHQw5cg1_Q0SjNEbKAiaRsS_CX6aCbgkrdU/edit?usp=sharing

#FigmaLink
https://www.figma.com/design/za3yRonBGoYl4Fsgbjugji/Census-Web?node-id=0-1&t=k6YAyMGTrmWwp2nx-1

# What to do:
- download and set-up Git
- check "Enable Git" in VSCode
- in VSCode:
  - click "View"
  - open "Command Palette"
  - select "Git: Clone"
  - paste: https://github.com/kyrstnxx/IMProject-CensusForm.git
  - generate new folder
  - open in VSCode
 
# Pull Main Branch
- check if you have existing Main branch
  - git branch

- if yes,
  - git checkout main
  - git pull

# Feature Branch 
- make sure youre starting from the latest code:
  - git checkout main
  - git pull origin main

- create your feature branch:
  - git checkout -b feature/my-feature
  - *do the coding and shiz

- you can now commit your changes:
  - git add 
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
  - git push origin feature/my-feature

# DELETE FEATURE BRANCH (OPTIONAL)
  - git branch -d feature/my-feature
=======
