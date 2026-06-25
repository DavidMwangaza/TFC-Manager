<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UDBL &mdash; Plateforme de Gestion des TFC</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    
    <!-- Google Font: Inter & Lora -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800|lora:400,500,600,700,800&display=swap" rel="stylesheet" />
    
    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-zinc-50 text-zinc-800 antialiased font-sans">

    {{-- BARRE INSTITUTIONNELLE --}}
    <div class="bg-gradient-to-r from-slate-900 via-primary-dark to-slate-900 text-slate-300 text-[10px] sm:text-xs py-2 shadow-sm relative z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row justify-between items-center gap-2">
            <span class="font-medium tracking-wide">Université Don Bosco de Lubumbashi &mdash; Année Académique {{ date('Y') }}-{{ date('Y') + 1 }}</span>
            <span class="flex items-center gap-1.5 uppercase tracking-wider font-bold text-accent text-[9px]">
                <span class="w-1.5 h-1.5 bg-accent rounded-full animate-ping"></span>
                <span>Portail académique sécurisé</span>
            </span>
        </div>
    </div>

    {{-- NAVBAR --}}
    <nav class="bg-white/80 backdrop-blur-md border-b border-slate-200/60 sticky top-0 z-40 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logos/logo_1.webp') }}" alt="UDBL" class="h-12 w-12 object-contain bg-slate-50 p-1 rounded-xl shadow-inner border border-slate-100">
                    <div>
                        <span class="text-lg font-extrabold text-slate-900 tracking-tight block leading-tight">TFC Manager</span>
                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider leading-none">Gestion des Cycles Académiques</span>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/dashboard') }}"
                           class="bg-primary hover:bg-primary-light text-white text-xs font-bold py-2.5 px-5 rounded-xl hover-lift shadow-md shadow-primary/10 transition-all">
                            Mon espace
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-slate-650 hover:text-slate-950 font-bold py-2.5 px-4 text-xs transition-colors">
                            Connexion
                        </a>
                        <a href="{{ route('archives.index') }}"
                           class="bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold py-2.5 px-5 rounded-xl border border-slate-200/60 shadow-sm transition-all hidden sm:inline-flex items-center gap-1">
                            <x-icon name="archive" class="h-4 w-4" />
                            <span>Archives</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    {{-- HERO SECTION --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-slate-900 via-primary-dark to-slate-950 py-16 lg:py-24 text-white">
        <!-- Abstract glowing circles -->
        <div class="absolute -right-24 -top-24 w-96 h-96 bg-accent/5 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -left-24 -bottom-24 w-96 h-96 bg-primary-light/10 rounded-full blur-3xl pointer-events-none"></div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">
                
                <!-- Left Content -->
                <div class="flex-1 text-center lg:text-left space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-white/5 backdrop-blur-md rounded-full text-xs font-semibold text-accent border border-white/10 tracking-wide">
                        <x-icon name="sparkles" class="h-4 w-4" />
                        <span>Propulsé par l'Intégrité IA</span>
                    </div>
                    
                    <h1 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-black leading-relaxed tracking-tight text-white">
                        Plateforme de Gestion des <br class="hidden lg:block" />
                        <span class="text-accent bg-clip-text">Travaux de Fin de Cycle</span>
                    </h1>
                    
                    <p class="text-base sm:text-lg text-slate-300 max-w-2xl leading-relaxed mx-auto lg:mx-0">
                        Soumettez vos fiches de sujet, planifiez vos livrables de rédaction avec suivi par jalons et validez vos manuscrits grâce à notre module d'analyse d'intégrité IA.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start pt-2">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="bg-accent hover:bg-amber-400 text-slate-950 font-extrabold py-3.5 px-8 rounded-xl text-sm transition-all shadow-md shadow-accent/20 hover-lift flex items-center justify-center gap-2">
                                <span>Accéder à mon espace</span>
                                <x-icon name="home" class="h-4 w-4 text-slate-950" />
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="bg-accent hover:bg-amber-400 text-slate-950 font-extrabold py-3.5 px-8 rounded-xl text-sm transition-all shadow-md shadow-accent/20 hover-lift flex items-center justify-center gap-2">
                                <span>Se connecter</span>
                                <x-icon name="lock-closed" class="h-4 w-4 text-slate-950" />
                            </a>
                        @endauth
                        <a href="{{ route('archives.index') }}"
                           class="bg-white/5 hover:bg-white/10 text-white font-bold py-3.5 px-8 rounded-xl text-sm border border-white/20 hover-lift transition-all flex items-center justify-center gap-2">
                            <x-icon name="archive" class="h-4 w-4 text-slate-350" />
                            <span>Consulter les archives publiques</span>
                        </a>
                    </div>
                </div>

                <!-- Right Visual: Logo box -->
                <div class="shrink-0 animate-fade-in-up">
                    <div class="bg-white/10 backdrop-blur-md rounded-3xl p-6 border border-white/15 shadow-2xl relative overflow-hidden group">
                        <div class="absolute -right-8 -top-8 w-24 h-24 bg-accent/20 rounded-full blur-xl"></div>
                        <div class="bg-white rounded-2xl p-6 shadow-inner relative z-10 transition-transform duration-500 group-hover:scale-105">
                            <img src="{{ asset('images/logos/logo_1.webp') }}" alt="UDBL" class="h-32 w-32 object-contain bg-white">
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ACCES PAR ROLE --}}
    <section class="py-16 bg-white border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-center text-xs font-bold text-slate-450 uppercase tracking-widest mb-10">Accès Personnalisés par Profil</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                
                <a href="{{ url('/dashboard') }}" class="glass-card hover-lift rounded-2xl p-6 border border-slate-150 flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div class="w-10 h-10 bg-primary/10 text-primary border border-primary/20 rounded-xl flex items-center justify-center mb-4 shrink-0 transition-transform duration-300 group-hover:scale-110">
                            <x-icon name="user" class="h-5 w-5" />
                        </div>
                        <h3 class="font-extrabold text-slate-800 text-base leading-snug">Espace Étudiant</h3>
                        <p class="text-xs text-slate-450 leading-relaxed">Soumission de proposition de sujet en 5 étapes, dépôt de livrables et consultation des diagnostics d'analyse IA.</p>
                    </div>
                    <span class="text-xs font-bold text-primary inline-flex items-center gap-1 mt-4 group-hover:underline">Se connecter →</span>
                </a>

                <a href="{{ url('/dashboard') }}" class="glass-card hover-lift rounded-2xl p-6 border border-slate-150 flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div class="w-10 h-10 bg-green-50 text-green-700 border border-green-100 rounded-xl flex items-center justify-center mb-4 shrink-0 transition-transform duration-300 group-hover:scale-110">
                            <x-icon name="check-badge" class="h-5 w-5" />
                        </div>
                        <h3 class="font-extrabold text-slate-800 text-base leading-snug">Espace Directeur</h3>
                        <p class="text-xs text-slate-450 leading-relaxed">Suivi des étudiants sous direction, vérification des SLA d'échéances et validation finale ("Feu Vert") avant la soutenance.</p>
                    </div>
                    <span class="text-xs font-bold text-green-600 inline-flex items-center gap-1 mt-4 group-hover:underline">Se connecter →</span>
                </a>

                <a href="{{ url('/dashboard') }}" class="glass-card hover-lift rounded-2xl p-6 border border-slate-150 flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div class="w-10 h-10 bg-blue-50 text-blue-700 border border-blue-100 rounded-xl flex items-center justify-center mb-4 shrink-0 transition-transform duration-300 group-hover:scale-110">
                            <x-icon name="building-library" class="h-5 w-5" />
                        </div>
                        <h3 class="font-extrabold text-slate-800 text-base leading-snug">Chef de Département</h3>
                        <p class="text-xs text-slate-450 leading-relaxed">Examen des fiches de proposition, validation ou rejet de sujet, affectation de directeurs et régulation des charges d'encadrement.</p>
                    </div>
                    <span class="text-xs font-bold text-blue-600 inline-flex items-center gap-1 mt-4 group-hover:underline">Se connecter →</span>
                </a>

                <a href="{{ url('/dashboard') }}" class="glass-card hover-lift rounded-2xl p-6 border border-slate-150 flex flex-col justify-between group">
                    <div class="space-y-4">
                        <div class="w-10 h-10 bg-slate-100 text-slate-700 border border-slate-200 rounded-xl flex items-center justify-center mb-4 shrink-0 transition-transform duration-300 group-hover:scale-110">
                            <x-icon name="cog-6-tooth" class="h-5 w-5" />
                        </div>
                        <h3 class="font-extrabold text-slate-800 text-base leading-snug">Administration</h3>
                        <p class="text-xs text-slate-450 leading-relaxed">Création de comptes utilisateurs, paramétrages système du module IA, gestion des facultés et filières académiques.</p>
                    </div>
                    <span class="text-xs font-bold text-slate-600 inline-flex items-center gap-1 mt-4 group-hover:underline">Se connecter →</span>
                </a>

            </div>
        </div>
    </section>

    {{-- STEPS TIMELINE PROCEDURE --}}
    <section class="py-16 bg-slate-50 border-b border-slate-200/60">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 space-y-2">
                <h2 class="text-2xl font-black text-slate-800 tracking-tight">Le Parcours Académique en 4 Étapes</h2>
                <p class="text-slate-400 max-w-xl mx-auto text-xs font-medium">Un processus connecté, collaboratif et transparent géré du début à la fin de votre cycle de recherche.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 relative">
                
                {{-- Step 1 --}}
                <div class="glass-card bg-white rounded-2xl p-6 border border-slate-150 text-center relative group">
                    <div class="w-10 h-10 bg-primary/10 text-primary border border-primary/20 font-black rounded-full flex items-center justify-center text-sm mx-auto mb-4 shrink-0">1</div>
                    <h3 class="font-extrabold text-slate-800 text-sm mb-1.5">Soumission du Sujet</h3>
                    <p class="text-xs text-slate-450 leading-relaxed">L'étudiant renseigne sa fiche de proposition structurée (contextes, question de recherche, objectifs, références) et valide.</p>
                    <span class="inline-flex items-center mt-3 text-[10px] font-bold uppercase tracking-wider text-primary">Étudiant</span>
                </div>

                {{-- Step 2 --}}
                <div class="glass-card bg-white rounded-2xl p-6 border border-slate-150 text-center relative group">
                    <div class="w-10 h-10 bg-green-50 text-green-700 border border-green-100 font-black rounded-full flex items-center justify-center text-sm mx-auto mb-4 shrink-0">2</div>
                    <h3 class="font-extrabold text-slate-800 text-sm mb-1.5">Arbitrage Académique</h3>
                    <p class="text-xs text-slate-450 leading-relaxed">La direction du département examine la recevabilité, valide le sujet et assigne officiellement le directeur principal.</p>
                    <span class="inline-flex items-center mt-3 text-[10px] font-bold uppercase tracking-wider text-green-600">Chef de Filière</span>
                </div>

                {{-- Step 3 --}}
                <div class="glass-card bg-white rounded-2xl p-6 border border-slate-150 text-center relative group">
                    <div class="w-10 h-10 bg-blue-50 text-blue-700 border border-blue-100 font-black rounded-full flex items-center justify-center text-sm mx-auto mb-4 shrink-0">3</div>
                    <h3 class="font-extrabold text-slate-800 text-sm mb-1.5">Livrables & Analyse IA</h3>
                    <p class="text-xs text-slate-450 leading-relaxed">Dépôt du PDF par jalons de rédaction. Chaque fichier fait l'objet d'un rapport de détection d'intégrité IA et de plagiat.</p>
                    <span class="inline-flex items-center mt-3 text-[10px] font-bold uppercase tracking-wider text-blue-600">Système TFC Manager</span>
                </div>

                {{-- Step 4 --}}
                <div class="glass-card bg-white rounded-2xl p-6 border border-slate-150 text-center relative group">
                    <div class="w-10 h-10 bg-amber-50 text-amber-700 border border-amber-100 font-black rounded-full flex items-center justify-center text-sm mx-auto mb-4 shrink-0">4</div>
                    <h3 class="font-extrabold text-slate-800 text-sm mb-1.5">Autorisation de Défense</h3>
                    <p class="text-xs text-slate-450 leading-relaxed">L'encadreur valide le manuscrit final en accordant le "Feu Vert" administratif pour planifier l'examen de soutenance devant le jury.</p>
                    <span class="inline-flex items-center mt-3 text-[10px] font-bold uppercase tracking-wider text-amber-600">Directeur d'Études</span>
                </div>

            </div>
        </div>
    </section>

    {{-- INTEGRITY IA SECTION --}}
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                
                <!-- Info Left -->
                <div class="space-y-6">
                    <h2 class="text-2xl font-black text-slate-800 tracking-tight leading-tight">Module d'Analyse d'Intégrité IA</h2>
                    <p class="text-slate-500 text-sm leading-relaxed">
                        Pour garantir l'excellence et l'authenticité des recherches de l'Université Don Bosco, chaque manuscrit déposé est analysé par nos algorithmes afin d'évaluer le taux d'utilisation de l'intelligence artificielle générative et de similarité textuelle.
                    </p>
                    
                    <div class="space-y-3.5 pt-2">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Classification de Risque</h3>
                        
                        <div class="flex items-center gap-3">
                            <span class="bg-green-50 text-green-700 text-xs font-extrabold px-3 py-1.5 rounded-lg border border-green-200 shrink-0 w-20 text-center">&lt; 20%</span>
                            <span class="text-xs text-slate-500 font-medium">Conforme : Taux de rédaction humaine original élevé.</span>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <span class="bg-amber-50 text-amber-700 text-xs font-extrabold px-3 py-1.5 rounded-lg border border-amber-200 shrink-0 w-20 text-center">20% - 50%</span>
                            <span class="text-xs text-slate-500 font-medium">Risque modéré : Vérification et révision de style recommandées.</span>
                        </div>
                        
                        <div class="flex items-center gap-3">
                            <span class="bg-red-50 text-red-700 text-xs font-extrabold px-3 py-1.5 rounded-lg border border-red-200 shrink-0 w-20 text-center">&gt; 50%</span>
                            <span class="text-xs text-slate-500 font-medium">Alerte critique : Entretien pédagogique avec le Directeur requis.</span>
                        </div>
                    </div>
                </div>

                <!-- Graphic Mockup Right -->
                <div class="bg-slate-50 border border-slate-200/60 rounded-3xl p-6 lg:p-8 shadow-inner relative overflow-hidden">
                    <div class="absolute -right-8 -top-8 w-24 h-24 bg-primary-light/5 rounded-full blur-xl"></div>
                    <h3 class="font-extrabold text-slate-700 text-xs uppercase tracking-wider mb-5 flex items-center gap-1.5">
                        <x-icon name="cpu-chip" class="h-5 w-5 text-blue-500" />
                        <span>Aperçu du Rapport de Conformité</span>
                    </h3>
                    
                    <div class="space-y-4">
                        <!-- Stat 1 -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-150 shadow-sm space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-700">Contenu Généré par IA (GPT/Claude)</span>
                                <span class="bg-green-50 text-green-700 text-xs font-black px-2.5 py-0.5 rounded-lg border border-green-100">12%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-green-500 h-2 rounded-full transition-all duration-1000" style="width: 12%"></div>
                            </div>
                        </div>

                        <!-- Stat 2 -->
                        <div class="bg-white rounded-2xl p-4 border border-slate-150 shadow-sm space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-slate-700">Taux de Similarité Académique (Plagiat)</span>
                                <span class="bg-amber-50 text-amber-700 text-xs font-black px-2.5 py-0.5 rounded-lg border border-amber-100">23%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-2">
                                <div class="bg-amber-500 h-2 rounded-full transition-all duration-1000" style="width: 23%"></div>
                            </div>
                        </div>
                        
                        <p class="text-[10px] text-slate-400 text-center font-semibold uppercase tracking-wider pt-2">
                            Analyse instantanée générée lors du dépôt du livrable PDF
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- INFORMATION GUIDELINES --}}
    <section class="bg-gradient-to-r from-slate-900 via-primary-dark to-slate-900 py-12 text-slate-300">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white/5 border border-white/10 rounded-2xl p-6 backdrop-blur-md">
                <h3 class="text-sm font-extrabold text-accent uppercase tracking-wider mb-4 flex items-center gap-1.5">
                    <x-icon name="information-circle" class="h-5 w-5 text-accent" />
                    <span>Informations Générales & Instructions</span>
                </h3>
                
                <ul class="space-y-3 text-xs leading-relaxed text-slate-350">
                    <li class="flex items-start gap-2">
                        <span class="text-accent shrink-0 font-bold">•</span>
                        <span><strong>Étudiants :</strong> Assurez-vous de disposer de vos identifiants d'inscription et de votre matricule UDBL pour activer votre accès personnel.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-accent shrink-0 font-bold">•</span>
                        <span><strong>Directeurs :</strong> Les habilitations d'accès enseignant sont configurées par le département informatique de votre faculté.</span>
                    </li>
                    <li class="flex items-start gap-2">
                        <span class="text-accent shrink-0 font-bold">•</span>
                        <span><strong>Formats Requis :</strong> Seuls les documents en format <strong class="text-white">PDF</strong> structurés de manière académique sont pris en charge.</span>
                    </li>
                </ul>
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="bg-slate-950 text-slate-400 py-12 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logos/logo_1.webp') }}" alt="UDBL" class="h-10 w-10 object-contain bg-white rounded-full p-0.5">
                        <span class="text-base font-extrabold text-white tracking-wide">UDBL TFC Manager</span>
                    </div>
                    <p class="text-xs leading-relaxed max-w-sm">
                        Système intégré de régulation, d'audit d'intégrité et d'archivage des Travaux de Fin de Cycle de l'Université Don Bosco de Lubumbashi.
                    </p>
                </div>
                
                <div class="space-y-3">
                    <h4 class="text-white font-extrabold text-xs uppercase tracking-wider">Facultés de rattachement</h4>
                    <ul class="space-y-1.5 text-xs">
                        <li>ESIS &mdash; Sciences Informatiques</li>
                        <li>ECOPO &mdash; Économie &amp; Finance</li>
                        <li>KANSEBULA &mdash; Sciences Humaines</li>
                        <li>THEOLOGICUM &mdash; Théologie</li>
                    </ul>
                </div>
                
                <div class="space-y-3">
                    <h4 class="text-white font-extrabold text-xs uppercase tracking-wider">Contact & Support</h4>
                    <ul class="space-y-1.5 text-xs">
                        <li>Lubumbashi, République Démocratique du Congo</li>
                        <li>Université Don Bosco</li>
                        <li class="font-bold text-accent">info@udbl.ac.cd</li>
                    </ul>
                </div>

            </div>
            
            <div class="border-t border-slate-900 mt-10 pt-6 text-center text-[10px] text-slate-500 font-semibold uppercase tracking-wider">
                &copy; {{ date('Y') }} Université Don Bosco de Lubumbashi &mdash; Tous droits réservés. Usage interne et exclusif.
            </div>
        </div>
    </footer>

</body>
</html>
