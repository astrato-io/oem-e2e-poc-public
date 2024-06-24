#QUICK START

1. To start the app, set the required variables in the .env.example file. The following document https://help.astrato.io/en/articles/9413603-auth-oem-api contains information on how to obtain the ID and secret.
```
  ASTRATO_CLIENT_ID=
  ASTRATO_CLIENT_SECRET=
  ASTRATO_URL=
  ASTRATO_EMBED_LINK=
```
2. Run following commands in terminal
```
docker compose build
docker compose up
```

#cheat-sheet
```
run > docker build -t getting-started .
docker run --env-file .env.example -dp 127.0.0.1:80:80 getting-started 
.debug now docker run --env-file .env.example --mount type=bind,src=./www/,dst=/var/www/html -dp 127.0.0.1:80:80 getting-started 

docker ps -a // to check the name
docker exec -it name bash // to get to bash console
```


  
  