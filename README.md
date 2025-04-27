# Dossier Technique – Projet GSB Frais

**Session BTS SIO 2025 – Épreuve E6 : Conception et développement d’applications (option SLAM)**  
**Développeur : Menoni Fabian**  
**Modalité : Individuelle**  
**Période de réalisation : Septembre 2024 – Avril 2025**  
**Environnement : PHP, SQL, TWIG, YAML / Symfony, Tailwind CSS, MySQL**  

---

## Table des Matières
1. [Présentation Générale du Projet](#1-présentation-générale-du-projet)
2. [Description de l’Architecture Technique](#2-description-de-larchitecture-technique)
   - 2.1. [Structure du Projet](#21-structure-du-projet)
   - 2.2. [Diagramme de Classes](#22-diagramme-de-classes)
3. [Détail des Fonctionnalités](#3-détail-des-fonctionnalités)
   - 3.1. [Saisie et Gestion des Frais](#31-saisie-et-gestion-des-frais)
   - 3.2. [Interface Administrateur et Statistiques](#32-interface-administrateur-et-statistiques)
4. [Analyse Approfondie des Contrôleurs](#4-analyse-approfondie-des-contrôleurs)
5. [Organisation du Code et des Templates](#5-organisation-du-code-et-des-templates)
6. [Maintenance et Évolutivité](#6-maintenance-et-évolutivité)
7. [Commentaires et Documentation Interne](#7-commentaires-et-documentation-interne)
8. [Conclusion et Perspectives d’Évolution](#8-conclusion-et-perspectives-dévolution)

---

## 1. Présentation Générale du Projet

Le projet **GSB Frais** a pour but de développer une application web de gestion des frais professionnels.  
Les objectifs principaux sont :

- **Conception et développement** : Créer une application intuitive permettant à chaque utilisateur de saisir, consulter et gérer ses frais.
- **Maintenance** : Concevoir une architecture modulaire facilitant la correction des bugs et l’ajout de nouvelles fonctionnalités.
- **Sécurité et gestion des données** : Garantir la sécurisation des données via une base MySQL et des bonnes pratiques de développement.

L’architecture de l’application repose sur le framework **Symfony** et utilise **TWIG** pour le rendu des vues ainsi que **Tailwind CSS** pour une interface responsive et moderne.

---

## 2. Description de l’Architecture Technique

### 2.1. Structure du Projet

La structure du projet se conforme aux standards Symfony et est organisée de la manière suivante :

- **/src**  
  Contient la logique métier, organisée par packages et par domaines fonctionnels.
  - **Controller/** : Contient les contrôleurs qui traitent les requêtes HTTP, gèrent la logique métier et renvoient les réponses.
  - **Entity/** : Définit les entités (modèles) liées à la base de données (ex. Frais, Utilisateur).
  - **Repository/** : Fournit des classes pour l’accès et la manipulation des données.

- **/templates**  
  Regroupe l’ensemble des vues TWIG qui composent l’interface utilisateur.
  - **base.html.twig** : Template de base utilisé par les autres vues.
  - **fiche_frais/** : Répertoire regroupant les templates spécifiques à la gestion des frais (saisie, édition, liste).
  - **transfert/** : Dossier mobilisant les différentes vues permettant l'import des données dans la base de données.

- **/config**  
  Contient les fichiers YAML de configuration pour Symfony (routes, services, paramètres, etc.).

- **/public**  
  Répertoire public servant les fichiers statiques (CSS généré par Tailwind CSS, images, etc.).

### 2.2. Diagramme de Classes

![diagramme](https://i.imgur.com/oTuYllB.png)

Ce diagramme illustre les principales entités du système et leurs relations.

---

## 3. Détail des Fonctionnalités

### 3.1. Saisie et Gestion des Frais

- **Saisie des Frais** :  
  Formulaire de saisie utilisant Symfony Forms et TWIG.  
  Validation des données effectuée côté serveur pour assurer l’intégrité des informations saisies.

- **Consultation et Édition** :  
  Liste des frais saisie par l’utilisateur, avec possibilité d’édition ou de suppression.
  
- **Traitement et Calcul** :  
  Intégration de règles métiers pour le calcul automatique du total des frais ou l’application de seuils de validation.

### 3.2. Interface Administrateur
  
- **Mécanismes de Validation** :  
  Contrôleurs dédiés permettent de valider ou de rejeter les frais, avec mise à jour de l’état en base de données.

---

## 4. Analyse Approfondie des Contrôleurs

Les contrôleurs jouent un rôle central dans l’architecture MVC :

- **Réception des Requêtes** :  
  Ils interceptent les requêtes HTTP entrantes et délèguent le traitement à la couche métier.

- **Traitement des Données** :  
  En utilisant les services et les entités, ils traitent les données (ex : validation des formulaires, récupération des données depuis la base).

- **Rendu des Réponses** :  
  Les contrôleurs renvoient des réponses, généralement sous la forme de vues rendues via TWIG.

- **Sécurité et Autorisations** :  
  Ils intègrent des vérifications de sécurité afin d’assurer que seules les personnes autorisées accèdent à certaines fonctionnalités.
  
---

## 5. Organisation du Code et des Templates

### 5.1. Dossier **/src**

- **Contrôleurs (/src/Controller/):**  
  Chaque contrôleur est codé en respectant les principes SOLID. Les méthodes sont commentées et structurées pour faciliter la maintenance.  
  *Conseil :* Une bonne pratique est d’utiliser les annotations pour la définition des routes, ce qui améliore la lisibilité.

- **Entités (/src/Entity/):**  
  Les classes entités représentent les tables de la base de données et sont annotées pour la configuration via Doctrine ORM.  
  *Exemple :* L’entité `Frais` contient des propriétés comme `montant`, `date`, et `description`.

- **Dépôts (/src/Repository/):**  
  Les repositories encapsulent toutes les requêtes SQL spécifiques à une entité, facilitant ainsi la réutilisation et la modification des requêtes.

- **Services (/src/Service/):**  
  Les services centralisent la logique métier complexe pouvant être réutilisée par plusieurs contrôleurs.

### 5.2. Dossier **/templates**

- **Base Template (base.html.twig) :**  
  Fournit la structure commune (header, footer, menus) utilisée par l’ensemble des vues.  
- **Templates Spécifiques :**  
  Les dossiers **frai/** et **admin/** contiennent respectivement les templates dédiés à la gestion des frais et à l’interface d’administration.  
  *Exemple :* `fiche_rais/new.html.twig` contient le formulaire de création d’un frais et `comptable/index.html.twig` permet de gérer les fiches frais en cours.
  
- **Utilisation de TWIG :**  
  Les templates TWIG utilisent des inclusions et des blocs pour assurer la réutilisation du code et faciliter les modifications ultérieures.

---

## 6. Maintenance et Évolutivité

### 6.1. Maintenance Corrective

- **Tests :**  
  Intégration de tests unitaires et fonctionnels pour détecter rapidement les anomalies.
- **Débogage :**  
  Utilisation de PHPStorm et du débogueur intégré pour identifier et corriger les erreurs.
- **Suivi des Bugs :**  
  Gestion des tickets via GitHub Issues pour une traçabilité des corrections.

### 6.2. Maintenance Évolutive

- **Extensibilité :**  
  La séparation des préoccupations (MVC, Services, Repositories) permet d’ajouter facilement de nouvelles fonctionnalités.  
- **Documentation interne :**  
  Les contrôleurs et services sont systématiquement commentés pour faciliter la reprise du code par un autre développeur.
- **Mises à jour de sécurité :**  
  Veille technologique régulière et application des correctifs sur Symfony, PHP et les bibliothèques utilisées.

---

## 7. Commentaires et Documentation Interne

- **Commentaires dans le Code :**  
  Chaque méthode critique du contrôleur (création, édition, suppression) est documentée avec une description des paramètres, du retour et des exceptions potentiellement lancées.
- **Documentation Automatique :**  
  Possibilité d’utiliser des outils comme PHPDoc pour générer une documentation à partir des commentaires du code.
- **Fichiers README Spécifiques :**  
  Chaque dossier (contrôleurs, entités, services) peut être accompagné d’un fichier README interne décrivant ses conventions et sa structure.

---

## 8. Conclusion et Perspectives d’Évolution

Ce dossier technique offre une vue d’ensemble complète du projet **GSB Frais**. Il détaille l’architecture, le fonctionnement des contrôleurs, l’organisation du code et la répartition des responsabilités, permettant ainsi à un nouveau développeur de prendre en main et de maintenir l’application.

**Perspectives d’évolution :**
- **Ajout de modules complémentaires** (par exemple, un module mobile ou une API externe).
- **Refactorisation et optimisation** continue du code base pour améliorer la performance.
- **Intégration d’outils de monitoring et de reporting** pour anticiper les anomalies et assurer une maintenance proactive.

---

*Fin du Dossier Technique*
