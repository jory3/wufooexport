# Wufoo Data Extraction Script

Extracts data of all Wufoo forms in an account to .xlsx-Files

### Instruction Set

> Clone this github repo.

> Copy the .env.exmaple file renaming it to .env. Change the APIKEY, PASSWORD, and SUBDOMAIN value to your provided account value.

```
APIKEY=
PASSWORD=
SUBDOMAIN=
```

> Once done, run a { composer install }.

> Finally, we will run the command { nohup php artisan wufoo:retrieve & }

### Author

> Akeem Palmer - [Phonics Software Solutions](https://phonicsolutions.com)
> Jonas Ryser - [Ryser Media](https://rymedia.ch/)
