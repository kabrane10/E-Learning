@extends('layouts.public')

@section('title', 'Conditions d\'utilisation')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Conditions d'utilisation</h1>
            <p class="text-gray-500 mb-8">Dernière mise à jour : {{ date('d/m/Y') }}</p>
            
            <div class="prose prose-lg max-w-none">
                <h2>1. Acceptation des conditions</h2>
                <p>En accédant à la plateforme E-Learn, vous acceptez d'être lié par les présentes conditions d'utilisation.</p>
                
                <h2>2. Description du service</h2>
                <p>E-Learn est une plateforme d'apprentissage en ligne permettant aux utilisateurs d'accéder à des cours gratuits et de suivre leur progression.</p>
                
                <h2>3. Compte utilisateur</h2>
                <p>Vous êtes responsable de maintenir la confidentialité de votre compte et de votre mot de passe.</p>
                
                <h2>4. Propriété intellectuelle</h2>
                <p>Tout le contenu disponible sur E-Learn est protégé par les lois sur la propriété intellectuelle.</p>
                
                <h2>5. Code de conduite</h2>
                <p>Les utilisateurs s'engagent à respecter les autres membres de la communauté et à ne pas publier de contenu inapproprié.</p>
                
                <h2>6. Limitation de responsabilité</h2>
                <p>E-Learn ne peut être tenu responsable des dommages indirects résultant de l'utilisation de la plateforme.</p>
                
                <h2>7. Modification des conditions</h2>
                <p>Nous nous réservons le droit de modifier ces conditions à tout moment.</p>
                
                <h2>8. Contact</h2>
                <p>Pour toute question concernant ces conditions, contactez-nous à legal@elearn.com.</p>
            </div>
        </div>
    </div>
</div>
@endsection