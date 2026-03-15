<template>
  <Navbar />
  <router-view />
  <AppFooter />
</template>

<script setup>
import Navbar from './components/navbar.vue'
import AppFooter from './page/Footer.vue'
</script>

<style scoped>
:deep(*) {
  --primary-color: #2563eb;
  --primary-dark: #1e40af;
  --primary-light: #3b82f6;
  --secondary-color: #7c3aed;
  --accent-color: #0891b2;
  --success: #10b981;
  --danger: #ef4444;
  --warning: #f59e0b;
  --dark: #1f2937;
  --light: #f3f4f6;
  --border: #e5e7eb;
}

:deep(body) {
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f9fafb;
  color: #1f2937;
  line-height: 1.6;
}

:deep(h1, h2, h3, h4, h5, h6) {
  font-weight: 600;
  color: #111827;
}

:deep(h1) {
  font-size: 2.5rem;
  margin-bottom: 1.5rem;
}

:deep(h2) {
  font-size: 2rem;
  margin-bottom: 1.25rem;
}

:deep(h3) {
  font-size: 1.5rem;
  margin-bottom: 1rem;
}

:deep(a) {
  color: var(--primary-color);
  text-decoration: none;
  transition: color 0.2s ease;
}

:deep(a:hover) {
  color: var(--primary-dark);
}

:deep(.btn) {
  font-weight: 500;
  border-radius: 8px;
  transition: all 0.3s ease;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
}

:deep(.btn:hover) {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  transform: translateY(-2px);
}

:deep(.btn-primary) {
  background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
  border: none;
  color: white;
}

:deep(.btn-primary:hover) {
  background: linear-gradient(135deg, var(--primary-dark) 0%, #6d28d9 100%);
  color: white;
}

:deep(.form-control, .form-select) {
  border: 2px solid var(--border);
  border-radius: 8px;
  padding: 0.75rem 1rem;
  font-size: 1rem;
  transition: all 0.3s ease;
}

:deep(.form-control:focus, .form-select:focus) {
  border-color: var(--primary-color);
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
  outline: none;
}

:deep(.card) {
  border: none;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
  transition: all 0.3s ease;
}

:deep(.card:hover) {
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
  transform: translateY(-4px);
}

:deep(.alert) {
  border: none;
  border-radius: 8px;
  border-left: 4px solid;
}

:deep(.alert-danger) {
  border-left-color: var(--danger);
  background: #fee2e2;
  color: #7f1d1d;
}

:deep(.alert-success) {
  border-left-color: var(--success);
  background: #dcfce7;
  color: #15803d;
}

:deep(.alert-warning) {
  border-left-color: var(--warning);
  background: #fef3c7;
  color: #92400e;
}

:deep(.badge) {
  border-radius: 20px;
  font-weight: 600;
  font-size: 0.85rem;
  padding: 0.4rem 0.8rem;
}
</style>

