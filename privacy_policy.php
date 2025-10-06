<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pages Légales - E-hianjaika</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Georgia', serif;
            background-color: #f8f9fa;
        }
        .page-section {
            display: none;
            background: white;
            padding: 40px;
            margin: 30px auto;
            max-width: 900px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 8px;
        }
        .page-section.active {
            display: block;
        }
        h1 {
            color: #D4AF37;
            font-size: 2.5rem;
            margin-bottom: 30px;
            border-bottom: 3px solid #D4AF37;
            padding-bottom: 15px;
        }
        h2 {
            color: #333;
            font-size: 1.8rem;
            margin-top: 30px;
            margin-bottom: 15px;
        }
        h3 {
            color: #555;
            font-size: 1.3rem;
            margin-top: 20px;
            margin-bottom: 10px;
        }
        p, li {
            line-height: 1.8;
            color: #666;
            margin-bottom: 15px;
        }
        .nav-tabs {
            margin-bottom: 30px;
            border-bottom: 2px solid #D4AF37;
        }
        .nav-tabs .nav-link {
            color: #666;
            border: none;
            padding: 15px 25px;
        }
        .nav-tabs .nav-link.active {
            color: #D4AF37;
            background-color: transparent;
            border-bottom: 3px solid #D4AF37;
            font-weight: bold;
        }
        .highlight-box {
            background-color: #fff9e6;
            border-left: 4px solid #D4AF37;
            padding: 20px;
            margin: 20px 0;
        }
        .last-updated {
            color: #999;
            font-style: italic;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <?php require_once ('views/layouts/header.php')?>

    <div class="container">
        <ul class="nav nav-tabs mt-4" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#cgv" onclick="showPage('cgv')">Conditions Générales</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#mentions" onclick="showPage('mentions')">Mentions Légales</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#confidentialite" onclick="showPage('confidentialite')">Politique de Confidentialité</a>
            </li>
        </ul>
        <div id="cgv" class="page-section active">
            <h1>Conditions Générales de Vente</h1>
            <p class="last-updated">Dernière mise à jour : Octobre 2025</p>

            <h2>1. Objet</h2>
            <p>Les présentes Conditions Générales de Vente (CGV) régissent les relations contractuelles entre E-hianjaika, maison de couture située à Antananarivo, Madagascar, et tout client souhaitant effectuer un achat sur notre site ou en boutique.</p>

            <h2>2. Produits et Services</h2>
            <h3>2.1 Description des produits</h3>
            <p>E-hianjaika propose :</p>
            <ul>
                <li>Des créations sur mesure en haute couture</li>
                <li>Des collections prêt-à-porter</li>
                <li>Des services de consultation et d'essayage</li>
                <li>Des retouches et ajustements</li>
            </ul>

            <h3>2.2 Prix</h3>
            <p>Les prix sont indiqués en Ariary malgache (MGA) et toutes taxes comprises. E-hianjaika se réserve le droit de modifier ses prix à tout moment, mais les produits seront facturés sur la base des tarifs en vigueur au moment de la validation de la commande.</p>

            <h2>3. Commandes</h2>
            <h3>3.1 Passation de commande</h3>
            <p>Toute commande implique l'acceptation pleine et entière des présentes CGV. Le client reconnaît avoir pris connaissance des présentes CGV et les avoir acceptées avant de passer commande.</p>

            <h3>3.2 Confirmation de commande</h3>
            <p>Après validation de votre commande, vous recevrez un email de confirmation récapitulant votre achat.</p>

            <h3>3.3 Créations sur mesure</h3>
            <div class="highlight-box">
                <p><strong>Important :</strong> Pour les créations sur mesure, un acompte de 50% est requis à la commande. Le solde sera à régler lors de la livraison finale. Les créations sur mesure ne sont ni reprises ni échangées.</p>
            </div>

            <h2>4. Paiement</h2>
            <h3>4.1 Modalités de paiement</h3>
            <p>Les paiements peuvent être effectués par :</p>
            <ul>
                <li>Carte bancaire</li>
                <li>Virement bancaire</li>
                <li>Espèces (en boutique uniquement)</li>
                <li>Mobile Money (MVola, Orange Money, Airtel Money)</li>
            </ul>

            <h3>4.2 Sécurité des paiements</h3>
            <p>Tous les paiements en ligne sont sécurisés. Vos données bancaires ne sont jamais stockées sur nos serveurs.</p>

            <h2>5. Livraison</h2>
            <h3>5.1 Zones de livraison</h3>
            <p>Nous livrons à Antananarivo et ses environs. Pour les autres régions de Madagascar, veuillez nous contacter.</p>

            <h3>5.2 Délais de livraison</h3>
            <ul>
                <li>Prêt-à-porter : 3 à 7 jours ouvrables</li>
                <li>Créations sur mesure : 2 à 6 semaines selon la complexité</li>
            </ul>

            <h3>5.3 Frais de livraison</h3>
            <p>Les frais de livraison sont calculés en fonction de la zone de livraison et du poids du colis. Ils sont indiqués avant la validation de la commande.</p>

            <h2>6. Droit de rétractation</h2>
            <p>Conformément à la législation en vigueur, vous disposez d'un délai de 14 jours à compter de la réception de votre commande pour exercer votre droit de rétractation, sauf pour :</p>
            <ul>
                <li>Les créations sur mesure</li>
                <li>Les produits personnalisés</li>
                <li>Les articles portés ou lavés</li>
            </ul>

            <h2>7. Retours et échanges</h2>
            <h3>7.1 Conditions de retour</h3>
            <p>Les produits doivent être retournés dans leur état d'origine, non portés, avec toutes les étiquettes attachées.</p>

            <h3>7.2 Procédure de retour</h3>
            <p>Pour effectuer un retour, contactez notre service client dans les 14 jours suivant la réception. Les frais de retour sont à la charge du client, sauf en cas de produit défectueux.</p>

            <h2>8. Garanties</h2>
            <p>Tous nos produits bénéficient de la garantie légale de conformité. En cas de défaut de fabrication, nous nous engageons à réparer ou remplacer le produit gratuitement dans un délai raisonnable.</p>

            <h2>9. Propriété intellectuelle</h2>
            <p>Tous les éléments présents sur le site E-hianjaika (textes, images, logos, créations) sont protégés par le droit d'auteur. Toute reproduction ou utilisation sans autorisation est interdite.</p>

            <h2>10. Protection des données personnelles</h2>
            <p>Vos données personnelles sont collectées et traitées conformément à notre Politique de Confidentialité. Vous disposez d'un droit d'accès, de rectification et de suppression de vos données.</p>

            <h2>11. Responsabilité</h2>
            <p>E-hianjaika ne saurait être tenue responsable des dommages indirects résultant de l'utilisation de nos produits ou services. Notre responsabilité est limitée au montant de la commande.</p>

            <h2>12. Litige et médiation</h2>
            <p>En cas de litige, nous privilégions une résolution amiable. Si aucun accord n'est trouvé, le litige sera soumis aux tribunaux compétents d'Antananarivo, Madagascar.</p>

            <h2>13. Modifications des CGV</h2>
            <p>E-hianjaika se réserve le droit de modifier les présentes CGV à tout moment. Les CGV applicables sont celles en vigueur à la date de la commande.</p>

            <h2>14. Contact</h2>
            <p>Pour toute question concernant nos CGV :<br>
            Email : shaliapage2025@gmail.com<br>
            Téléphone : +261 34 63 460 50<br>
            Adresse : Antananarivo Mahazoarivo</p>
        </div>
        <div id="mentions" class="page-section">
            <h1>Mentions Légales</h1>
            <p class="last-updated">Dernière mise à jour : Octobre 2025</p>

            <h2>1. Identification de l'entreprise</h2>
            <p><strong>Nom de l'entreprise :</strong> E-hianjaika<br>
            <strong>Forme juridique :</strong> [SARL / EI / Autre]<br>
            <strong>Siège social :</strong> Mahazoarivo, Antananarivo, Madagascar<br>
            <strong>NIF :</strong> [Numéro d'Identification Fiscale]<br>
            <strong>STAT :</strong> [Numéro STAT]<br>
            <strong>Email :</strong> shaliapage2025@gmail.com<br>
            <strong>Téléphone :</strong> +261 34 63 460 50</p>

            <h2>2. Directeur de la publication</h2>
            <p><strong>Nom :</strong> [Nom du directeur]<br>
            <strong>Qualité :</strong> [Gérant / Directeur]</p>

            <h2>3. Hébergement du site</h2>
            <p><strong>Hébergeur :</strong> [Nom de l'hébergeur]<br>
            <strong>Adresse :</strong> [Adresse de l'hébergeur]<br>
            <strong>Téléphone :</strong> [Téléphone de l'hébergeur]</p>

            <h2>4. Propriété intellectuelle</h2>
            <p>L'ensemble du contenu de ce site (textes, images, vidéos, logos, graphismes, etc.) est la propriété exclusive d'E-hianjaika ou de ses partenaires. Toute reproduction, distribution, modification, adaptation, retransmission ou publication de ces différents éléments est strictement interdite sans l'accord écrit préalable d'E-hianjaika.</p>

            <h3>4.1 Créations originales</h3>
            <p>Les créations de mode présentées sur ce site sont des œuvres originales protégées par le droit d'auteur. Leur reproduction, même partielle, sans autorisation expresse constitue une contrefaçon.</p>

            <h2>5. Crédits</h2>
            <p><strong>Design et développement :</strong> [Nom de l'agence ou développeur]<br>
            <strong>Photographies :</strong> [Nom du photographe ou "Photos propriétaires"]<br>
            <strong>Icônes :</strong> Font Awesome</p>

            <h2>6. Cookies</h2>
            <p>Ce site utilise des cookies pour améliorer l'expérience utilisateur. En naviguant sur ce site, vous acceptez l'utilisation de cookies conformément à notre Politique de Confidentialité.</p>

            <h2>7. Limitation de responsabilité</h2>
            <p>E-hianjaika s'efforce d'assurer l'exactitude et la mise à jour des informations diffusées sur ce site. Toutefois, E-hianjaika ne peut garantir l'exactitude, la précision ou l'exhaustivité des informations mises à disposition sur ce site.</p>

            <h3>7.1 Disponibilité du site</h3>
            <p>E-hianjaika ne peut être tenue responsable des interruptions temporaires du site pour des raisons de maintenance, de mise à jour ou de problèmes techniques.</p>

            <h3>7.2 Liens externes</h3>
            <p>Les liens hypertextes présents sur le site vers d'autres sites ne sauraient engager la responsabilité d'E-hianjaika quant au contenu de ces sites.</p>

            <h2>8. Loi applicable</h2>
            <p>Les présentes mentions légales sont régies par la loi malgache. Tout litige relatif à l'utilisation de ce site sera soumis à la juridiction des tribunaux compétents de Madagascar.</p>

            <h2>9. Contact</h2>
            <p>Pour toute question concernant les mentions légales :<br>
            Email : shaliapage2025@gmail.com<br>
            Téléphone : +26134 63 460 50</p>
        </div>
        <div id="confidentialite" class="page-section">
            <h1>Politique de Confidentialité</h1>
            <p class="last-updated">Dernière mise à jour : Octobre 2025</p>

            <div class="highlight-box">
                <p><strong>Notre engagement :</strong> E-hianjaika s'engage à protéger la confidentialité et la sécurité de vos données personnelles. Cette politique explique comment nous collectons, utilisons et protégeons vos informations.</p>
            </div>

            <h2>1. Responsable du traitement</h2>
            <p><strong>Identité :</strong> E-hianjaika<br>
            <strong>Adresse :</strong> Mahazoarivo, Antananarivo, Madagascar<br>
            <strong>Email :</strong>shaliapage2025@gmail.com<br>
            <strong>Téléphone :</strong> +261 34 63 460 50</p>

            <h2>2. Données collectées</h2>
            <h3>2.1 Données d'identification</h3>
            <ul>
                <li>Nom et prénom</li>
                <li>Adresse email</li>
                <li>Numéro de téléphone</li>
                <li>Adresse postale</li>
                <li>Date de naissance (optionnel)</li>
            </ul>

            <h3>2.2 Données de commande</h3>
            <ul>
                <li>Historique des achats</li>
                <li>Préférences de taille et de style</li>
                <li>Mesures pour les créations sur mesure</li>
                <li>Informations de paiement (traitées de manière sécurisée)</li>
            </ul>

            <h3>2.3 Données de navigation</h3>
            <ul>
                <li>Adresse IP</li>
                <li>Type de navigateur</li>
                <li>Pages visitées</li>
                <li>Durée de visite</li>
                <li>Données de cookies</li>
            </ul>

            <h2>3. Finalités du traitement</h2>
            <p>Vos données sont collectées pour :</p>
            <ul>
                <li>Traiter vos commandes et gérer votre compte client</li>
                <li>Améliorer nos services et personnaliser votre expérience</li>
                <li>Vous envoyer des informations sur nos produits et promotions (avec votre consentement)</li>
                <li>Respecter nos obligations légales et comptables</li>
                <li>Prévenir la fraude et assurer la sécurité de notre site</li>
                <li>Réaliser des statistiques et analyses</li>
            </ul>

            <h2>4. Base légale du traitement</h2>
            <p>Le traitement de vos données repose sur :</p>
            <ul>
                <li><strong>L'exécution d'un contrat :</strong> pour le traitement de vos commandes</li>
                <li><strong>Votre consentement :</strong> pour l'envoi de newsletters et communications marketing</li>
                <li><strong>Nos intérêts légitimes :</strong> pour l'amélioration de nos services</li>
                <li><strong>Une obligation légale :</strong> pour la conservation des données comptables</li>
            </ul>

            <h2>5. Destinataires des données</h2>
            <p>Vos données peuvent être transmises à :</p>
            <ul>
                <li>Notre personnel autorisé</li>
                <li>Nos prestataires de services (hébergement, paiement, livraison)</li>
                <li>Les autorités compétentes sur demande légale</li>
            </ul>
            <p><strong>Important :</strong> Nous ne vendons jamais vos données à des tiers.</p>

            <h2>6. Durée de conservation</h2>
            <ul>
                <li><strong>Données de compte client :</strong> Pendant toute la durée de la relation commerciale + 3 ans</li>
                <li><strong>Données de commande :</strong> 10 ans (obligations comptables)</li>
                <li><strong>Données de navigation :</strong> 13 mois maximum</li>
                <li><strong>Données marketing :</strong> 3 ans après le dernier contact</li>
            </ul>

            <h2>7. Sécurité des données</h2>
            <p>Nous mettons en œuvre des mesures techniques et organisationnelles pour protéger vos données :</p>
            <ul>
                <li>Chiffrement SSL pour les transmissions de données</li>
                <li>Sécurisation des serveurs et bases de données</li>
                <li>Accès restreint aux données personnelles</li>
                <li>Sauvegardes régulières</li>
                <li>Formation du personnel à la protection des données</li>
            </ul>

            <h2>8. Vos droits</h2>
            <p>Conformément à la réglementation en vigueur, vous disposez des droits suivants :</p>

            <h3>8.1 Droit d'accès</h3>
            <p>Vous pouvez obtenir une copie de vos données personnelles que nous détenons.</p>

            <h3>8.2 Droit de rectification</h3>
            <p>Vous pouvez demander la correction de données inexactes ou incomplètes.</p>

            <h3>8.3 Droit à l'effacement</h3>
            <p>Vous pouvez demander la suppression de vos données dans certaines circonstances.</p>

            <h3>8.4 Droit d'opposition</h3>
            <p>Vous pouvez vous opposer au traitement de vos données à des fins de marketing direct.</p>

            <h3>8.5 Droit à la portabilité</h3>
            <p>Vous pouvez recevoir vos données dans un format structuré et couramment utilisé.</p>

            <h3>8.6 Droit de limitation</h3>
            <p>Vous pouvez demander la limitation du traitement de vos données dans certains cas.</p>

            <h3>8.7 Exercice de vos droits</h3>
            <p>Pour exercer vos droits, contactez-nous :<br>
            Email : privacy@ehianjaika.com<br>
            Courrier : E-hianjaika, [Adresse complète], Antananarivo, Madagascar</p>
            <p>Nous répondrons à votre demande dans un délai de 30 jours.</p>

            <h2>9. Cookies</h2>
            <h3>9.1 Types de cookies utilisés</h3>
            <ul>
                <li><strong>Cookies essentiels :</strong> nécessaires au fonctionnement du site</li>
                <li><strong>Cookies de performance :</strong> pour analyser l'utilisation du site</li>
                <li><strong>Cookies marketing :</strong> pour personnaliser les publicités (avec votre consentement)</li>
            </ul>

            <h3>9.2 Gestion des cookies</h3>
            <p>Vous pouvez gérer vos préférences de cookies via les paramètres de votre navigateur ou notre bandeau de consentement.</p>

            <h2>10. Transferts internationaux</h2>
            <p>Vos données sont principalement stockées à Madagascar. Si un transfert international est nécessaire (par exemple, pour l'hébergement cloud), nous garantissons un niveau de protection adéquat.</p>

            <h2>11. Mineurs</h2>
            <p>Nos services ne sont pas destinés aux personnes de moins de 18 ans. Nous ne collectons pas sciemment de données concernant des mineurs.</p>

            <h2>12. Modifications de la politique</h2>
            <p>Nous nous réservons le droit de modifier cette politique à tout moment. Les modifications importantes vous seront notifiées par email ou via une notification sur le site.</p>

            <h2>13. Contact et réclamation</h2>
            <p>Pour toute question concernant cette politique ou pour déposer une réclamation :</p>
            <p><strong>Email :</strong> shaliapage2025@gmail.com<br>
            <strong>Téléphone :</strong> +261 34 63 460 50<br>
            <strong>Adresse :</strong> Mahazoarivo, Antananarivo, Madagascar</p>

            <p>Si vous estimez que vos droits n'ont pas été respectés, vous pouvez introduire une réclamation auprès de l'autorité de protection des données compétente de Madagascar.</p>
        </div>
    </div>
    <?php include_once ('views/layouts/footer.php')?>

    <script>
        function showPage(pageId) {
            document.querySelectorAll('.page-section').forEach(section => {
                section.classList.remove('active');
            });
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            document.getElementById(pageId).classList.add('active');
            event.target.classList.add('active');
            window.scrollTo(0, 0);
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>