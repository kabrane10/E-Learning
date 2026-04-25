@extends('layouts.instructor')

@section('title', 'Mon Profil')
@section('page-title', 'Mon Profil')

@section('breadcrumb')
<nav class="flex" aria-label="Breadcrumb">
    <ol class="flex items-center space-x-2">
        <li><a href="{{ route('instructor.dashboard') }}" class="text-gray-400 hover:text-gray-500"><i class="fas fa-home"></i></a></li>
        <li><i class="fas fa-chevron-right text-gray-300 text-xs"></i></li>
        <li class="text-sm font-medium text-gray-700">Mon Profil</li>
    </ol>
</nav>
@endsection

@push('styles')
<style>
    .avatar-upload { transition: all 0.3s ease; }
    .avatar-upload:hover .avatar-overlay { opacity: 1; }
    .avatar-overlay { opacity: 0; transition: opacity 0.3s ease; background: rgba(0, 0, 0, 0.5); }
    .password-toggle { transition: color 0.2s ease; }
    .password-toggle:hover { color: #4f46e5; }
    .social-input { transition: all 0.2s ease; }
    .social-input:focus-within { border-color: #4f46e5; box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1); }
</style>
@endpush

@section('content')
<div x-data="profileEditor()" x-init="init()" class="max-w-4xl mx-auto">
    
    <!-- Toast Notification -->
    <div x-show="toast.show" x-transition x-cloak
         class="fixed top-20 right-5 z-50">
        <div :class="toast.type === 'success' ? 'bg-green-50 border-green-400 text-green-700' : 'bg-red-50 border-red-400 text-red-700'"
             class="border-l-4 p-4 rounded-r-lg shadow-lg flex items-center max-w-md">
            <i :class="toast.type === 'success' ? 'fas fa-check-circle text-green-500' : 'fas fa-exclamation-circle text-red-500'" class="mr-3 text-lg"></i>
            <span x-text="toast.message"></span>
            <button @click="toast.show = false" class="ml-4 text-gray-400 hover:text-gray-600"><i class="fas fa-times"></i></button>
        </div>
    </div>

    <!-- ============================================ -->
    <!-- CARTE PRINCIPALE                              -->
    <!-- ============================================ -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">

        <!-- En-tête avec avatar -->
        <div class="bg-gradient-to-br from-indigo-600 via-purple-600 to-pink-500 px-6 lg:px-8 py-8 lg:py-10">
            <div class="flex flex-col sm:flex-row items-center gap-6 lg:gap-8">
                <!-- Avatar -->
                <div class="relative group">
                    <div class="relative cursor-pointer" @click="$refs.avatarInput.click()">
                        <img :src="avatarPreview || '{{ auth()->user()->avatar }}'" 
                             class="w-24 h-24 lg:w-28 lg:h-28 rounded-full border-4 border-white shadow-xl object-cover transition-all duration-200 group-hover:brightness-75">
                        <div class="absolute inset-0 rounded-full flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <i class="fas fa-camera text-white text-xl lg:text-2xl"></i>
                        </div>
                    </div>
                    <span x-show="avatarFile" x-transition
                          class="absolute -bottom-1 -right-1 w-7 h-7 lg:w-8 lg:h-8 bg-green-500 rounded-full border-2 border-white flex items-center justify-center shadow-md">
                        <i class="fas fa-check text-white text-xs"></i>
                    </span>
                    <button type="button" x-show="hasAvatar || avatarPreview" @click.stop="removeAvatar"
                            class="absolute -top-1 -right-1 w-6 h-6 lg:w-7 lg:h-7 bg-red-500 text-white rounded-full border-2 border-white flex items-center justify-center shadow-md hover:bg-red-600 transition-colors">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                    <input type="file" x-ref="avatarInput" @change="handleAvatarUpload" 
                           accept="image/jpeg,image/png,image/gif,image/webp" class="hidden">
                </div>
                
                <!-- Infos -->
                <div class="text-white text-center sm:text-left flex-1">
                    <h2 class="text-2xl lg:text-3xl font-bold">{{ auth()->user()->name }}</h2>
                    <p class="text-indigo-100 mt-1 flex items-center justify-center sm:justify-start gap-2">
                        <i class="fas fa-briefcase text-sm"></i>
                        <span x-text="form.title || 'Ajoutez un titre professionnel'">{{ auth()->user()->title ?: 'Ajoutez un titre professionnel' }}</span>
                    </p>
                    <p class="text-indigo-200 text-sm mt-1.5 flex items-center justify-center sm:justify-start gap-2">
                        <i class="fas fa-envelope text-xs"></i><span>{{ auth()->user()->email }}</span>
                    </p>
                    <div class="flex items-center justify-center sm:justify-start gap-3 mt-3">
                        <button type="button" @click="$refs.avatarInput.click()"
                                class="text-xs bg-white/20 backdrop-blur-sm hover:bg-white/30 text-white px-3 py-1.5 rounded-lg transition-colors">
                            <i class="fas fa-camera mr-1.5"></i>Changer la photo
                        </button>
                        <button type="button" x-show="hasAvatar || avatarPreview" @click="removeAvatar"
                                class="text-xs bg-white/20 backdrop-blur-sm hover:bg-red-500/30 text-white px-3 py-1.5 rounded-lg transition-colors">
                            <i class="fas fa-trash mr-1.5"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================ -->
        <!-- FORMULAIRE PRINCIPAL (INFOS PERSO + SOCIAUX) -->
        <!-- ============================================ -->
        <form @submit.prevent="saveProfile" class="p-8">
            
            <!-- Informations personnelles -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user text-indigo-600 text-sm"></i>
                    </span>
                    Informations personnelles
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nom complet <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400"><i class="fas fa-user"></i></span>
                            <input type="text" x-model="form.name" required 
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-3 top-3 text-gray-400"><i class="fas fa-envelope"></i></span>
                            <input type="email" x-model="form.email" required 
                                   class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Titre professionnel</label>
                    <div class="relative">
                        <span class="absolute left-3 top-3 text-gray-400"><i class="fas fa-briefcase"></i></span>
                        <input type="text" x-model="form.title" 
                               class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Ex: Développeur Full Stack & Formateur">
                    </div>
                </div>
                
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Bio</label>
                    <textarea x-model="form.bio" rows="4" 
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none"
                              placeholder="Parlez de vous, de votre expérience..."></textarea>
                </div>
            </div>

            <!-- Réseaux sociaux -->
            <div class="mb-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-share-alt text-blue-600 text-sm"></i>
                    </span>
                    Réseaux sociaux
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div><label class="block text-sm font-medium text-gray-700 mb-2">Site web</label><div class="relative"><span class="absolute left-3 top-3 text-gray-400"><i class="fas fa-globe"></i></span><input type="url" x-model="form.website" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl" placeholder="https://..."></div></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-2">Twitter</label><div class="relative"><span class="absolute left-3 top-3 text-sky-500"><i class="fab fa-twitter"></i></span><input type="text" x-model="form.twitter" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl" placeholder="@username"></div></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-2">LinkedIn</label><div class="relative"><span class="absolute left-3 top-3 text-blue-600"><i class="fab fa-linkedin"></i></span><input type="text" x-model="form.linkedin" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl" placeholder="linkedin.com/in/..."></div></div>
                    <div><label class="block text-sm font-medium text-gray-700 mb-2">YouTube</label><div class="relative"><span class="absolute left-3 top-3 text-red-600"><i class="fab fa-youtube"></i></span><input type="text" x-model="form.youtube" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-xl" placeholder="@chaine"></div></div>
                </div>
            </div>

            <!-- Bouton Enregistrer -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="submit" :disabled="isSubmitting"
                        class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md font-medium disabled:opacity-50">
                    <i class="fas fa-spinner fa-spin mr-2" x-show="isSubmitting"></i>
                    <i class="fas fa-save mr-2" x-show="!isSubmitting"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>

    <!-- ============================================ -->
    <!-- CHANGEMENT DE MOT DE PASSE (CARTE SÉPARÉE)    -->
    <!-- ============================================ -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden mt-8">
        <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
            <div class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-md">
                <i class="fas fa-lock text-white text-lg"></i>
            </div>
            <div>
                <h3 class="font-semibold text-gray-900 text-lg">Changer le mot de passe</h3>
                <p class="text-sm text-gray-500">Assurez-vous d'utiliser un mot de passe fort et unique</p>
            </div>
        </div>
        
        <form @submit.prevent="updatePassword" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
                <!-- Mot de passe actuel -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-key mr-2 text-indigo-500"></i>Mot de passe actuel
                    </label>
                    <div class="relative">
                        <input :type="passwordForm.show_current ? 'text' : 'password'" 
                               x-model="passwordForm.current_password" required
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="••••••••">
                        <button type="button" @click="passwordForm.show_current = !passwordForm.show_current"
                                class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                            <i class="fas text-lg" :class="passwordForm.show_current ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Nouveau mot de passe -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-lock mr-2 text-indigo-500"></i>Nouveau mot de passe
                    </label>
                    <div class="relative">
                        <input :type="passwordForm.show_new ? 'text' : 'password'" 
                               x-model="passwordForm.new_password" required minlength="8"
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="Min. 8 caractères">
                        <button type="button" @click="passwordForm.show_new = !passwordForm.show_new"
                                class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                            <i class="fas text-lg" :class="passwordForm.show_new ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <div x-show="passwordForm.new_password.length > 0" class="mt-2">
                        <div class="flex gap-1">
                            <template x-for="i in 4" :key="i">
                                <div class="h-1.5 flex-1 rounded-full transition-all duration-300"
                                     :class="getPasswordStrengthClass(i)"></div>
                            </template>
                        </div>
                        <p class="text-xs mt-1" :class="getPasswordStrengthText()" x-text="getPasswordStrengthLabel()"></p>
                    </div>
                </div>
                
                <!-- Confirmer -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="fas fa-check-circle mr-2 text-indigo-500"></i>Confirmer
                    </label>
                    <div class="relative">
                        <input :type="passwordForm.show_confirm ? 'text' : 'password'" 
                               x-model="passwordForm.new_password_confirmation" required minlength="8"
                               class="w-full px-4 py-3 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                               placeholder="••••••••">
                        <button type="button" @click="passwordForm.show_confirm = !passwordForm.show_confirm"
                                class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600">
                            <i class="fas text-lg" :class="passwordForm.show_confirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    <p x-show="passwordForm.new_password_confirmation.length > 0" class="text-xs mt-1"
                       :class="passwordForm.new_password === passwordForm.new_password_confirmation ? 'text-green-600' : 'text-red-500'">
                        <span x-show="passwordForm.new_password === passwordForm.new_password_confirmation">
                            <i class="fas fa-check-circle mr-1"></i>Les mots de passe correspondent
                        </span>
                        <span x-show="passwordForm.new_password !== passwordForm.new_password_confirmation">
                            <i class="fas fa-times-circle mr-1"></i>Les mots de passe ne correspondent pas
                        </span>
                    </p>
                </div>
            </div>
            
            <!-- Conseils -->
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <i class="fas fa-shield-alt text-blue-600 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-medium text-blue-800 mb-1">Conseils pour un mot de passe fort :</p>
                        <ul class="text-xs text-blue-700 space-y-0.5">
                            <li>• Au moins 8 caractères</li>
                            <li>• Mélangez majuscules, minuscules et chiffres</li>
                            <li>• Ajoutez des caractères spéciaux (!@#$%^&*)</li>
                            <li>• Évitez les informations personnelles</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" :disabled="passwordForm.loading || !isPasswordValid()"
                        class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-md font-medium disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-spinner fa-spin mr-2" x-show="passwordForm.loading"></i>
                    <i class="fas fa-save mr-2" x-show="!passwordForm.loading"></i>
                    Mettre à jour le mot de passe
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
 function profileEditor() {
    return {
        // Toast
        toast: { show: false, type: 'success', message: '' },
        
        // Formulaire profil
        form: {
            name: '{{ auth()->user()->name }}',
            email: '{{ auth()->user()->email }}',
            title: '{{ auth()->user()->title ?? '' }}',
            bio: '{{ addslashes(auth()->user()->bio) ?? '' }}',
            website: '{{ auth()->user()->website ?? '' }}',
            twitter: '{{ auth()->user()->twitter ?? '' }}',
            linkedin: '{{ auth()->user()->linkedin ?? '' }}',
            youtube: '{{ auth()->user()->youtube ?? '' }}',
        },
        isSubmitting: false,
        
        // Avatar
        avatarPreview: null,
        avatarFile: null,
        hasAvatar: {{ auth()->user()->getFirstMediaUrl('avatar') ? 'true' : 'false' }},
        removeAvatarFlag: false,
        
        // Mot de passe
        passwordForm: {
            current_password: '', new_password: '', new_password_confirmation: '',
            show_current: false, show_new: false, show_confirm: false,
            loading: false,
        },
        
        init() { console.log('✅ Profil initialisé'); },
        
        // Avatar methods
        handleAvatarUpload(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (!['image/jpeg','image/png','image/gif','image/webp'].includes(file.type)) {
                alert('Format non supporté'); return;
            }
            if (file.size > 2*1024*1024) { alert('Max 2MB'); return; }
            this.avatarFile = file; this.removeAvatarFlag = false; this.hasAvatar = true;
            const r = new FileReader(); r.onload = e => this.avatarPreview = e.target.result; r.readAsDataURL(file);
        },
        removeAvatar() {
            this.avatarFile = null; this.avatarPreview = null; this.removeAvatarFlag = true; this.hasAvatar = false;
            if(this.$refs.avatarInput) this.$refs.avatarInput.value = '';
        },
        
        // Save profile
        async saveProfile() {
            if(this.isSubmitting) return;
            if(!this.form.name||!this.form.email){alert('Nom et email obligatoires');return;}
            this.isSubmitting=true;
            const fd=new FormData(); fd.append('_method','PUT');
            fd.append('name',this.form.name);fd.append('email',this.form.email);
            fd.append('title',this.form.title||'');fd.append('bio',this.form.bio||'');
            fd.append('website',this.form.website||'');fd.append('twitter',this.form.twitter||'');
            fd.append('linkedin',this.form.linkedin||'');fd.append('youtube',this.form.youtube||'');
            if(this.avatarFile) fd.append('avatar',this.avatarFile);
            if(this.removeAvatarFlag) fd.append('remove_avatar','1');
            fd.append('_token',document.querySelector('meta[name="csrf-token"]').content);
            try{
                const r=await fetch('{{route("instructor.profile.update")}}',{method:'POST',body:fd,headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
                const d=await r.json();
                if(d.success){alert('✅ '+d.message);setTimeout(()=>{window.location.href=window.location.href.split('?')[0]+'?t='+Date.now()},1000);}
                else {alert('❌ '+(d.message||'Erreur'));}
            }catch(e){console.error(e);alert('Erreur connexion');}
            finally{this.isSubmitting=false;}
        },
        
        // Password methods
        isPasswordValid(){return this.passwordForm.current_password.length>0&&this.passwordForm.new_password.length>=8&&this.passwordForm.new_password===this.passwordForm.new_password_confirmation;},
        getPasswordStrength(){const p=this.passwordForm.new_password;if(!p)return 0;let s=0;if(p.length>=8)s++;if(/[a-z]/.test(p))s++;if(/[A-Z]/.test(p))s++;if(/[0-9]/.test(p))s++;if(/[!@#$%^&*(),.?\":{}|<>]/.test(p))s++;return Math.min(s,4);},
        getPasswordStrengthClass(l){const s=this.getPasswordStrength();return s>=l?(s<=2?'bg-red-500':s===3?'bg-amber-500':'bg-green-500'):'bg-gray-200';},
        getPasswordStrengthText(){const s=this.getPasswordStrength();return s<=1?'text-red-500':s===2?'text-amber-500':s===3?'text-green-500':'text-green-600';},
        getPasswordStrengthLabel(){const l=['Très faible','Faible','Moyen','Fort','Très fort'];return l[this.getPasswordStrength()]||'Très faible';},
        async updatePassword(){
            if(!this.passwordForm.current_password){alert('Mot de passe actuel requis');return;}
            if(!this.passwordForm.new_password||this.passwordForm.new_password.length<8){alert('8 caractères minimum');return;}
            if(this.passwordForm.new_password!==this.passwordForm.new_password_confirmation){alert('Les mots de passe ne correspondent pas');return;}
            this.passwordForm.loading=true;
            const fd=new FormData();fd.append('current_password',this.passwordForm.current_password);
            fd.append('new_password',this.passwordForm.new_password);fd.append('new_password_confirmation',this.passwordForm.new_password_confirmation);
            fd.append('_token',document.querySelector('meta[name="csrf-token"]').content);
            try{
                const r=await fetch('{{route("instructor.profile.password")}}',{method:'POST',body:fd,headers:{'X-Requested-With':'XMLHttpRequest','Accept':'application/json'}});
                const d=await r.json();
                if(r.ok&&d.success){alert('✅ '+d.message);this.passwordForm.current_password='';this.passwordForm.new_password='';this.passwordForm.new_password_confirmation='';}
                else{alert('❌ '+(d.message||'Erreur'));}
            }catch(e){console.error(e);alert('Erreur connexion');}
            finally{this.passwordForm.loading=false;}
        }
    };
}
</script>
@endpush