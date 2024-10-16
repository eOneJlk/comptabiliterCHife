-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 16 oct. 2024 à 09:28
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `db_hostel`
--

-- --------------------------------------------------------

--
-- Structure de la table `agents`
--

CREATE TABLE `agents` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `departement` varchar(50) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `date_embauche` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `mot_de_passe` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `agents`
--

INSERT INTO `agents` (`id`, `nom`, `prenom`, `email`, `telephone`, `adresse`, `departement`, `role`, `date_embauche`, `created_at`, `mot_de_passe`) VALUES
(1, 'birego', 'Christian', 'christianbirego3@gmail.com', '0974336652', 'nord-kivu\\r\\nGoma', 'lundry', 'agent', '2024-10-12', '2024-10-12 11:51:14', ''),
(3, 'Habamungu', 'Christian', 'numericbtech@gmail.com', '0974336652', 'Av muchacha', 'stock', 'admin', '2024-10-22', '2024-10-12 12:02:06', ''),
(5, 'birego', 'Christian', 'numericbtech1@gmail.com', '0974336652', 'nord-kivu12', 'stock', 'admin', '2024-10-10', '2024-10-13 19:41:20', '$2y$10$hSgHg4tT1/LAZZgdGzlVEuYG8sms22iydzrALjSQ2T7BMSWwdTPSW');

-- --------------------------------------------------------

--
-- Structure de la table `depenses`
--

CREATE TABLE `depenses` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `etat` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `depenses`
--

INSERT INTO `depenses` (`id`, `date`, `nom`, `description`, `amount`, `etat`, `created_at`) VALUES
(1, '2024-10-15', 'ACHAT MATERIEL', '0', 39990.00, 'rejected', '2024-10-15 17:08:36');

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `id` int(11) NOT NULL,
  `date_paiement` date DEFAULT NULL,
  `nom_agent` varchar(100) DEFAULT NULL,
  `categorie` varchar(50) DEFAULT NULL,
  `montant` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id`, `date_paiement`, `nom_agent`, `categorie`, `montant`, `created_at`) VALUES
(1, '2024-10-27', 'birego Christian', 'payment', 300.00, '2024-10-12 12:09:20'),
(2, '2024-10-27', 'birego Christian', 'payment', 300.00, '2024-10-12 12:09:50'),
(3, '2024-09-30', 'Habamungu Christian', 'avance', 400.00, '2024-10-12 12:51:40'),
(4, '2024-09-30', 'Habamungu Christian', 'avance', 400.00, '2024-10-12 12:53:49');

-- --------------------------------------------------------

--
-- Structure de la table `produits`
--

CREATE TABLE `produits` (
  `id` int(11) NOT NULL,
  `date_entree` date NOT NULL,
  `nom_produit` varchar(255) NOT NULL,
  `quantite` int(11) NOT NULL,
  `emplacement_stock` varchar(255) NOT NULL,
  `date_sortie` date DEFAULT NULL,
  `date_modification` datetime DEFAULT NULL,
  `id_modificateur` int(11) DEFAULT NULL,
  `date_suppression` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `produits`
--

INSERT INTO `produits` (`id`, `date_entree`, `nom_produit`, `quantite`, `emplacement_stock`, `date_sortie`, `date_modification`, `id_modificateur`, `date_suppression`) VALUES
(1, '2024-10-12', 'boisson', 60, 'resto', NULL, '2024-10-15 17:28:59', 5, NULL),
(2, '2024-10-15', 'nouriture', 0, 'resto', '2024-10-15', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `rapports`
--

CREATE TABLE `rapports` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `nom` varchar(100) NOT NULL,
  `nombre_check_out` int(11) NOT NULL,
  `nombre_check_in` int(11) NOT NULL,
  `chambre_disponible` int(11) NOT NULL,
  `contenu` text NOT NULL,
  `entree_cash` decimal(10,2) NOT NULL,
  `credit` decimal(10,2) NOT NULL,
  `entree_airtel_money` decimal(10,2) NOT NULL,
  `entree_carte_pos` decimal(10,2) NOT NULL,
  `agent_id` int(11) NOT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `rapports`
--

INSERT INTO `rapports` (`id`, `date`, `nom`, `nombre_check_out`, `nombre_check_in`, `chambre_disponible`, `contenu`, `entree_cash`, `credit`, `entree_airtel_money`, `entree_carte_pos`, `agent_id`, `submitted_at`) VALUES
(1, '2024-10-16', 'birego', 2, 6, 50, 'chambre libre', 3600.00, 0.00, 260.00, 50.00, 5, '2024-10-16 05:30:48');

-- --------------------------------------------------------

--
-- Structure de la table `transactions_bancaires`
--

CREATE TABLE `transactions_bancaires` (
  `id` int(11) NOT NULL,
  `transaction_type` enum('deposit','withdrawal') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `date` date NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `slip_number` varchar(50) NOT NULL,
  `etat` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `transactions_bancaires`
--

INSERT INTO `transactions_bancaires` (`id`, `transaction_type`, `amount`, `date`, `invoice_number`, `slip_number`, `etat`, `created_at`) VALUES
(1, 'deposit', 2344.00, '2024-10-15', '2343647489', '121234534', 'approved', '2024-10-15 17:24:44');

-- --------------------------------------------------------

--
-- Structure de la table `transactions_caisse`
--

CREATE TABLE `transactions_caisse` (
  `id` int(11) NOT NULL,
  `date` date NOT NULL,
  `type` enum('entree','sortie') NOT NULL,
  `montant` decimal(10,2) NOT NULL,
  `details` text NOT NULL,
  `compte` varchar(50) NOT NULL,
  `etat` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `transactions_caisse`
--

INSERT INTO `transactions_caisse` (`id`, `date`, `type`, `montant`, `details`, `compte`, `etat`, `created_at`) VALUES
(2, '2024-10-15', 'entree', 2003.00, 'dd', 'DG', 'rejected', '2024-10-15 16:31:37'),
(3, '2024-10-15', 'sortie', 24000.00, 'PAIEMENT AGENTS', '1234567890', 'approved', '2024-10-15 17:07:31'),
(4, '2024-10-14', 'entree', 99999999.99, 'd', 'DG', 'rejected', '2024-10-15 17:48:27');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `depenses`
--
ALTER TABLE `depenses`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `produits`
--
ALTER TABLE `produits`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `rapports`
--
ALTER TABLE `rapports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `agent_id` (`agent_id`);

--
-- Index pour la table `transactions_bancaires`
--
ALTER TABLE `transactions_bancaires`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `transactions_caisse`
--
ALTER TABLE `transactions_caisse`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `depenses`
--
ALTER TABLE `depenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `produits`
--
ALTER TABLE `produits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `rapports`
--
ALTER TABLE `rapports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `transactions_bancaires`
--
ALTER TABLE `transactions_bancaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `transactions_caisse`
--
ALTER TABLE `transactions_caisse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `rapports`
--
ALTER TABLE `rapports`
  ADD CONSTRAINT `rapports_ibfk_1` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
