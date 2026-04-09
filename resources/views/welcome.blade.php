<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UDBL &mdash; Plateforme de Gestion des TFC</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased">

    {{-- BARRE INSTITUTIONNELLE --}}
    <div class="bg-blue-900 text-blue-100 text-xs py-1.5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <span>Universit&eacute; Don Bosco de Lubumbashi &mdash; Ann&eacute;e acad&eacute;mique {{ date('Y') }}-{{ date('Y') + 1 }}</span>
            <span>Plateforme r&eacute;serv&eacute;e aux membres de l'universit&eacute;</span>
        </div>
    </div>

    {{-- NAVBAR --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logos/logo_1.webp') }}" alt="UDBL" class="h-10 w-10 object-contain">
                    <div>
                        <span class="text-lg font-bold text-gray-800 block leading-tight">TFC Manager</span>
                        <span class="text-xs text-gray-400 leading-tight">Gestion des Travaux de Fin de Cycle</span>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 px-5 rounded-lg text-sm transition">
                            Mon espace
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-gray-600 hover:text-gray-900 font-medium py-2 px-4 text-sm transition">
                            Connexion
                        </a>

                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- EN-TETE INSTITUTIONNEL --}}
    <section class="bg-gradient-to-br from-blue-800 via-blue-700 to-blue-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-20">
            <div class="flex flex-col lg:flex-row items-center gap-10">
                <div class="flex-shrink-0">
                    <div class="bg-white rounded-2xl p-4 shadow-lg">
                        <img src="{{ asset('images/logos/logo_1.webp') }}" alt="UDBL" class="h-28 w-28 object-contain">
                    </div>
                </div>
                <div class="text-center lg:text-left">
                    <p class="text-blue-200 text-sm font-medium tracking-wide uppercase mb-2">Universit&eacute; Don Bosco de Lubumbashi</p>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-white leading-tight mb-4">
                        Plateforme de Gestion des <span class="text-yellow-300">Travaux de Fin de Cycle</span>
                    </h1>
                    <p class="text-lg text-blue-100 max-w-2xl leading-relaxed mb-8">
                        Espace de soumission des sujets, de suivi des validations, de d&eacute;p&ocirc;t des m&eacute;moires
                        et de v&eacute;rification par analyse IA, destin&eacute; aux &eacute;tudiants, enseignants et responsables acad&eacute;miques.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold py-3 px-8 rounded-lg text-base transition shadow">
                                Acc&eacute;der &agrave; mon espace
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="bg-yellow-400 hover:bg-yellow-300 text-gray-900 font-bold py-3 px-8 rounded-lg text-base transition shadow">
                                Se connecter
                            </a>
                        @endauth
                        <a href="{{ route('archives.index') }}"
                           class="bg-white/20 hover:bg-white/30 text-white font-bold py-3 px-8 rounded-lg text-base transition shadow border border-white/30">
                            <svg class="w-5 h-5 inline-block mr-1 -mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
                            Consulter les archives
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ACCES PAR ROLE --}}
    <section class="bg-white border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h2 class="text-center text-lg font-semibold text-gray-500 uppercase tracking-wide mb-8">Acc&egrave;s selon votre profil</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-blue-50 rounded-xl p-5 border border-blue-100">
                    <div class="w-10 h-10 bg-blue-600 text-white rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342" /></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">&Eacute;tudiant</h3>
                    <p class="text-sm text-gray-500">Soumettre un sujet, d&eacute;poser le m&eacute;moire, consulter les rapports d'analyse.</p>
                </div>
                <div class="bg-green-50 rounded-xl p-5 border border-green-100">
                    <div class="w-10 h-10 bg-green-600 text-white rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Chef de Fili&egrave;re</h3>
                    <p class="text-sm text-gray-500">Valider ou rejeter les sujets, assigner les directeurs de m&eacute;moire.</p>
                </div>
                <div class="bg-purple-50 rounded-xl p-5 border border-purple-100">
                    <div class="w-10 h-10 bg-purple-600 text-white rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0" /></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Enseignant</h3>
                    <p class="text-sm text-gray-500">Suivre les travaux encadr&eacute;s, consulter les analyses IA, autoriser la soutenance.</p>
                </div>
                <div class="bg-gray-50 rounded-xl p-5 border border-gray-200">
                    <div class="w-10 h-10 bg-gray-700 text-white rounded-lg flex items-center justify-center mb-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75" /></svg>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1">Administrateur</h3>
                    <p class="text-sm text-gray-500">G&eacute;rer les utilisateurs, les fili&egrave;res, les ann&eacute;es acad&eacute;miques et les param&egrave;tres.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- PROCEDURE --}}
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Proc&eacute;dure de d&eacute;p&ocirc;t de TFC</h2>
                <p class="text-gray-500 max-w-2xl mx-auto">Le processus se d&eacute;roule en quatre &eacute;tapes successives, chacune impliquant un acteur diff&eacute;rent.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="relative bg-white rounded-xl p-6 border border-gray-200 text-center">
                    <div class="w-10 h-10 bg-blue-700 text-white rounded-full flex items-center justify-center text-sm font-bold mx-auto mb-3">1</div>
                    <h3 class="font-semibold text-gray-900 mb-1">Soumission du sujet</h3>
                    <p class="text-sm text-gray-500">L'&eacute;tudiant remplit le formulaire structur&eacute; (5 &eacute;tapes) et soumet son sujet.</p>
                    <span class="text-xs text-blue-600 font-medium mt-2 inline-block">&Eacute;tudiant</span>
                </div>
                <div class="relative bg-white rounded-xl p-6 border border-gray-200 text-center">
                    <div class="w-10 h-10 bg-green-700 text-white rounded-full flex items-center justify-center text-sm font-bold mx-auto mb-3">2</div>
                    <h3 class="font-semibold text-gray-900 mb-1">Validation acad&eacute;mique</h3>
                    <p class="text-sm text-gray-500">Le Chef de Fili&egrave;re examine le sujet, le valide ou le rejette avec motif.</p>
                    <span class="text-xs text-green-600 font-medium mt-2 inline-block">Chef de Fili&egrave;re</span>
                </div>
                <div class="relative bg-white rounded-xl p-6 border border-gray-200 text-center">
                    <div class="w-10 h-10 bg-purple-700 text-white rounded-full flex items-center justify-center text-sm font-bold mx-auto mb-3">3</div>
                    <h3 class="font-semibold text-gray-900 mb-1">D&eacute;p&ocirc;t et analyse</h3>
                    <p class="text-sm text-gray-500">L'&eacute;tudiant d&eacute;pose son m&eacute;moire (PDF). Le syst&egrave;me effectue une analyse IA automatique.</p>
                    <span class="text-xs text-purple-600 font-medium mt-2 inline-block">&Eacute;tudiant &rarr; Syst&egrave;me</span>
                </div>
                <div class="relative bg-white rounded-xl p-6 border border-gray-200 text-center">
                    <div class="w-10 h-10 bg-yellow-600 text-white rounded-full flex items-center justify-center text-sm font-bold mx-auto mb-3">4</div>
                    <h3 class="font-semibold text-gray-900 mb-1">Autorisation de soutenance</h3>
                    <p class="text-sm text-gray-500">L'enseignant directeur v&eacute;rifie le rapport d'analyse et autorise la soutenance.</p>
                    <span class="text-xs text-yellow-600 font-medium mt-2 inline-block">Enseignant</span>
                </div>
            </div>
        </div>
    </section>

    {{-- ANALYSE IA --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">V&eacute;rification par analyse IA</h2>
                    <p class="text-gray-500 mb-6 leading-relaxed">
                        Chaque m&eacute;moire d&eacute;pos&eacute; est automatiquement soumis &agrave; un module de d&eacute;tection de contenu
                        g&eacute;n&eacute;r&eacute; par intelligence artificielle. Le rapport d'analyse est mis &agrave; disposition
                        de l'enseignant directeur et du Chef de Fili&egrave;re.
                    </p>
                    <div class="space-y-3">
                        <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">Grille d'interpr&eacute;tation</h3>
                        <div class="flex items-center gap-3">
                            <span class="bg-green-100 text-green-800 text-xs font-bold px-3 py-1.5 rounded-full w-16 text-center">&lt; 20%</span>
                            <span class="text-sm text-gray-600">Risque faible &mdash; Contenu consid&eacute;r&eacute; comme original</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-3 py-1.5 rounded-full w-16 text-center">20-50%</span>
                            <span class="text-sm text-gray-600">Risque mod&eacute;r&eacute; &mdash; V&eacute;rification recommand&eacute;e</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="bg-red-100 text-red-800 text-xs font-bold px-3 py-1.5 rounded-full w-16 text-center">&gt; 50%</span>
                            <span class="text-sm text-gray-600">Risque &eacute;lev&eacute; &mdash; Entretien avec l'&eacute;tudiant requis</span>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                    <h3 class="font-semibold text-gray-700 text-sm uppercase tracking-wide mb-4">Exemple de rapport d'analyse</h3>
                    <div class="space-y-4">
                        <div class="bg-white rounded-lg p-4 border">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Score de contenu IA</span>
                                <span class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-1 rounded-full">12%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full" style="width: 12%"></div>
                            </div>
                        </div>
                        <div class="bg-white rounded-lg p-4 border">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Score de similarit&eacute;</span>
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-1 rounded-full">23%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-yellow-500 h-2 rounded-full" style="width: 23%"></div>
                            </div>
                        </div>
                        <p class="text-xs text-gray-400 text-center mt-2">Analyse effectu&eacute;e automatiquement lors du d&eacute;p&ocirc;t</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- INFORMATIONS PRATIQUES --}}
    <section class="bg-blue-800 py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white/10 rounded-xl p-6 border border-white/10">
                <h2 class="text-lg font-semibold text-white mb-3">Informations pratiques</h2>
                <ul class="space-y-2 text-blue-100 text-sm">
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-yellow-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                        <span>Les &eacute;tudiants doivent s'inscrire avec leur <strong class="text-white">matricule universitaire</strong> pour acc&eacute;der &agrave; la plateforme.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-yellow-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                        <span>Les comptes enseignants et chefs de fili&egrave;re sont cr&eacute;&eacute;s par l'administration. Contactez votre facult&eacute; si n&eacute;cessaire.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-yellow-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                        <span>Les fichiers accept&eacute;s sont au format <strong class="text-white">PDF uniquement</strong>.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-yellow-300 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" /></svg>
                        <span>En cas de probl&egrave;me technique, contactez l'administration &agrave; <strong class="text-white">info@udbl.ac.cd</strong>.</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-gray-900 text-gray-400 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div>
                    <div class="flex items-center gap-3 mb-3">
                        <img src="{{ asset('images/logos/logo_1.webp') }}" alt="UDBL" class="h-9 w-9 object-contain">
                        <span class="text-base font-bold text-white">UDBL TFC Manager</span>
                    </div>
                    <p class="text-sm leading-relaxed">
                        Plateforme interne de gestion des Travaux de Fin de Cycle &mdash;
                        Universit&eacute; Don Bosco de Lubumbashi.
                    </p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3 text-sm">Facult&eacute;s</h4>
                    <ul class="space-y-1.5 text-sm">
                        <li>ESIS &mdash; Sciences Informatiques</li>
                        <li>ECOPO &mdash; &Eacute;conomie &amp; Finance</li>
                        <li>KANSEBULA &mdash; Sciences Humaines</li>
                        <li>THEOLOGICUM &mdash; Th&eacute;ologie</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3 text-sm">Contact</h4>
                    <ul class="space-y-1.5 text-sm">
                        <li>Lubumbashi, R&eacute;publique D&eacute;mocratique du Congo</li>
                        <li>Universit&eacute; Don Bosco</li>
                        <li>info@udbl.ac.cd</li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-6 text-center text-xs text-gray-500">
                &copy; {{ date('Y') }} Universit&eacute; Don Bosco de Lubumbashi &mdash; Plateforme TFC Manager. Usage interne.
            </div>
        </div>
    </footer>

</body>
</html>
