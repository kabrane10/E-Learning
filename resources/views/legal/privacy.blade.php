@extends('layouts.public')

@section('title', 'Politique de confidentialité')

@section('content')
<div class="bg-gray-50 min-h-screen py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-2xl shadow-sm p-8 md:p-12">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Politique de confidentialité</h1>
            <p class="text-gray-500 mb-8">Dernière mise à jour : {{ date('d/m/Y') }}</p>
            
            <div class="prose prose-lg max-w-none">
                <h2>1. Collecte des données</h2>
                <p>Nous collectons les informations que vous nous fournissez lors de votre inscription : nom, email, et données de progression.</p>
                
                <h2>2. Utilisation des données</h2>
                <p>Vos données sont utilisées pour personnaliser votre expérience d'apprentissage et améliorer nos services.</p>
                
                <h2>3. Protection des données</h2>
                <p>Nous mettons en œuvre des mesures de sécurité pour protéger vos informations personnelles.</p>
                
                <h2>4. Cookies</h2>
                <p>Nous utilisons des cookies pour améliorer votre expérience sur notre plateforme.</p>
                
                <h2>5. Partage des données</h2>
                <p>Nous ne vendons pas vos données personnelles à des tiers.</p>
                
                <h2>6. Vos droits</h2>
                <p>Vous avez le droit d'accéder, de rectifier et de supprimer vos données personnelles.</p>
                
                <h2>7. Contact</h2>
                <p>Pour toute question concernant votre vie privée : privacy@elearn.com.</p>
            </div>
        </div>
    </div>
</div>
@endsection