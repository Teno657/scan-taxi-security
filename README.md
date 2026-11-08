# Plateforme d'Audit - Vérification des Taxis par QR Code

## Description
Cette application web est une reproduction de la plateforme de vérification des taxis par QR Code de la Communauté Urbaine de Yaoundé. Elle a été développée dans un but pédagogique pour analyser et démontrer les failles de sécurité d'un système de scan public.

## Fonctionnalités
- Génération de QR Codes uniques pour les taxis
- Téléchargement d'autocollants prêts à imprimer (PNG)
- Page de scan et de vérification (statut "Vérifié")
- Interface bilingue (Français / Anglais)
- Affichage de l'identifiant du chauffeur
- Stockage des données dans une base MySQL

## Technologies utilisées
- PHP (Backend)
- MySQL (Base de données)
- HTML / CSS / JavaScript (Frontend)
- Librairie `phpqrcode` (Génération des QR Codes)
- Librairie `html2canvas` (Capture d'images)
- AJAX

## Installation
1. Cloner le projet dans le dossier `htdocs` de XAMPP.
2. Importer la base de données MySQL (fichier `database.sql`).
3. Configurer le fichier `db_connect.php` avec vos identifiants.
4. Démarrer Apache et MySQL sur XAMPP.
5. Lancer le projet sur : `http://localhost/scan-taxi/index.php`

## Guide d'utilisation
- **Page d'accueil** : Cliquez sur "Générer un QR Code".
- **Génération** : Un numéro aléatoire et un QR Code sont créés. Téléchargez l'autocollant.
- **Scan** : Utilisez un smartphone pour scanner le QR Code.
- **Vérification** : La page affiche le statut "Vérifié" et l'identifiant du chauffeur.

## Auteur
Fotso Teno Herman

Étudiant en Réseau, Système et Cybersécurité – HINTEL
