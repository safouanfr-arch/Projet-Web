<template>
  <div class="login-wrapper">
    <div class="login-card">
      <!-- État connecté -->
      <div v-if="user" class="connected-state text-center">
        <div class="success-icon">✓</div>
        <h2 class="connected-title">Connecté avec succès</h2>
        <p class="welcome-text">Bienvenue, <strong>{{ user.name }}</strong></p>
        <div v-if="user.role" class="role-badge">
          <span class="role-label">Rôle :</span>
          <span class="role-value">{{ user.role.toUpperCase() }}</span>
        </div>
        <div class="d-grid mt-5">
          <button class="btn-logout-primary" @click="handleLogout">Se déconnecter</button>
        </div>
        <p class="continue-text mt-4">Vous pouvez continuer à parcourir le site</p>
      </div>

      <!-- État non connecté : formulaire -->
      <div v-else class="login-form-container">
        <div class="form-header text-center mb-5">
          <h2 class="login-title">Connexion</h2>
          <p class="subtitle">Accédez à votre espace personnel</p>
        </div>

        <div v-if="errorMsg" class="alert-error">
          <span class="error-icon">⚠</span>
          {{ errorMsg }}
        </div>

        <form @submit.prevent="handleLogin" class="login-form">
          <!-- Login -->
          <div class="form-group">
            <label for="login" class="form-label">Identifiant</label>
            <input
              id="login"
              type="text"
              v-model="loginName"
              placeholder="Entrez votre login"
              class="form-input"
              required
            >
          </div>

          <!-- Mot de passe -->
          <div class="form-group">
            <label for="password" class="form-label">Mot de passe</label>
            <input
              id="password"
              type="password"
              v-model="password"
              placeholder="Entrez votre mot de passe"
              class="form-input"
              required
            >
          </div>

          <!-- Se souvenir de moi -->
          <div class="form-check-custom mb-4">
            <input
              type="checkbox"
              id="remember"
              v-model="rememberMe"
              class="checkbox-input"
            >
            <label for="remember" class="checkbox-label">Se souvenir de moi</label>
          </div>

          <!-- Bouton connexion -->
          <button type="submit" class="btn-submit" :disabled="loading">
            <span v-if="!loading">Se connecter</span>
            <span v-else>
              <span class="spinner"></span> Connexion en cours...
            </span>
          </button>
        </form>

        <div class="demo-credentials">
          <p><strong>Identifiants de démonstration :</strong></p>
          <div class="credential-item">
            <span class="role-tag admin">Admin</span>
            <code>admin / admin</code>
          </div>
          <div class="credential-item">
            <span class="role-tag user">User</span>
            <code>user / user</code>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { login, logout, getSession } from '../api.js'
import { clearDetailCache } from '../components/detail.js'

const loginName = ref('')
const password = ref('')
const rememberMe = ref(false)
const user = ref(null)
const errorMsg = ref('')
const loading = ref(false)

// Charger l'état de session au montage initial et enregistrer listener session-changed
onMounted(async () => {
  // Charger l'état de la session existante
  try {
    const data = await getSession()
    if (data.success && data.logged_in) {
      user.value = data.user
    }
  } catch (e) {
    console.error('Erreur session:', e)
  }

  // Enregistrer le listener pour les changements de session cross-component
  window.addEventListener('session-changed', onSessionChanged)
})

async function handleLogin() {
  if (!loginName.value || !password.value) {
    errorMsg.value = 'Veuillez remplir tous les champs !'
    return
  }
  loading.value = true
  errorMsg.value = ''
  try {
    const data = await login(loginName.value, password.value)
    if (data.success) {
      user.value = data.user
      loginName.value = ''
      password.value = ''
      clearDetailCache()
      // Notifier Navbar et autres composants de la connexion
      window.dispatchEvent(new CustomEvent('session-changed'))
    } else {
      errorMsg.value = data.error || 'Identifiants incorrects'
    }
  } catch (e) {
    errorMsg.value = 'Erreur de connexion au serveur'
    console.error('Erreur login:', e)
  } finally {
    loading.value = false
  }
}

async function handleLogout() {
  try {
    await logout()
    user.value = null
    clearDetailCache()
    // Notifier Navbar et autres composants de la déconnexion
    window.dispatchEvent(new CustomEvent('session-changed'))
  } catch (e) {
    console.error('Erreur logout:', e)
  }
}


// Synchroniser l'affichage quand la session change (login/logout depuis Navbar)
// Cet event listener permet à Connexion.vue de réagir aux changements de session sans refresh
function onSessionChanged() {
  getSession().then(data => {
    if (data.success && data.logged_in) {
      user.value = data.user
    } else {
      user.value = null
    }
  }).catch(() => {})
}

onUnmounted(() => {
  window.removeEventListener('session-changed', onSessionChanged)
})
</script>

<style scoped>
.login-wrapper {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 2rem;
  margin-top: 0;
}

.login-card {
  width: 100%;
  max-width: 450px;
  background: white;
  border-radius: 16px;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
  overflow: hidden;
  animation: slideUp 0.5s ease;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Connected state */
.connected-state {
  padding: 3rem 2rem;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
}

.success-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 3rem;
  color: white;
  margin: 0 auto 1.5rem;
  animation: scaleIn 0.6s ease;
}

@keyframes scaleIn {
  from {
    transform: scale(0);
  }
  to {
    transform: scale(1);
  }
}

.connected-title {
  color: #1f2937;
  font-size: 1.75rem;
  margin-bottom: 1rem;
}

.welcome-text {
  color: #4b5563;
  font-size: 1.1rem;
  margin-bottom: 1.5rem;
}

.role-badge {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 20px;
  font-weight: 600;
  margin-bottom: 2rem;
}

.role-label {
  opacity: 0.9;
}

.role-value {
  font-weight: 700;
  letter-spacing: 0.5px;
}

.btn-logout-primary {
  background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
  color: white;
  border: none;
  padding: 0.875rem 2rem;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
}

.btn-logout-primary:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
}

.continue-text {
  color: #6b7280;
  font-size: 0.95rem;
}

/* Login form */
.login-form-container {
  padding: 3rem 2rem;
}

.form-header {
  margin-bottom: 2rem;
}

.login-title {
  color: #1f2937;
  font-size: 2rem;
  margin-bottom: 0.5rem;
  font-weight: 700;
}

.subtitle {
  color: #6b7280;
  font-size: 1rem;
}

.alert-error {
  background: #fee2e2;
  border-left: 4px solid #ef4444;
  color: #7f1d1d;
  padding: 1rem;
  border-radius: 8px;
  margin-bottom: 1.5rem;
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.error-icon {
  font-size: 1.25rem;
}

.login-form {
  margin-bottom: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-label {
  display: block;
  color: #374151;
  font-weight: 600;
  font-size: 0.95rem;
  margin-bottom: 0.5rem;
}

.form-input {
  width: 100%;
  padding: 0.875rem 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  font-size: 1rem;
  font-family: inherit;
  transition: all 0.3s ease;
  background: #f9fafb;
}

.form-input:focus {
  outline: none;
  border-color: #667eea;
  background: white;
  box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-check-custom {
  display: flex;
  align-items: center;
  gap: 0.75rem;
}

.checkbox-input {
  width: 20px;
  height: 20px;
  cursor: pointer;
  accent-color: #667eea;
}

.checkbox-label {
  color: #4b5563;
  font-size: 0.95rem;
  cursor: pointer;
  user-select: none;
}

.btn-submit {
  width: 100%;
  padding: 1rem;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.75rem;
}

.btn-submit:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-submit:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.spinner {
  display: inline-block;
  width: 1rem;
  height: 1rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top: 2px solid white;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Demo credentials */
.demo-credentials {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e5e7eb;
  text-align: center;
  background: #f9fafb;
  padding: 1rem;
  border-radius: 8px;
}

.demo-credentials p {
  color: #1f2937;
  font-weight: 600;
  margin-bottom: 1rem;
}

.credential-item {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 0.75rem;
}

.role-tag {
  padding: 0.25rem 0.75rem;
  border-radius: 4px;
  font-size: 0.75rem;
  font-weight: 700;
  color: white;
}

.role-tag.admin {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.role-tag.user {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.credential-item code {
  background: white;
  padding: 0.5rem 1rem;
  border-radius: 4px;
  font-family: 'Courier New', monospace;
  color: #1f2937;
  font-weight: 600;
  border: 1px solid #e5e7eb;
}
</style>

