#!/bin/bash

# Vérifie si un message de commit est fourni
if [ -z "$1" ]; then
    echo "Erreur : Aucun message de commit fourni."
    echo "Usage: ./commit-add.sh 'Ton message de commit'"
    exit 1
fi

# Ajoute tous les fichiers modifiés/supprimés
git add .

# Crée le commit avec le message fourni
git commit -m "$1"

echo "Commit effectué : $1"
