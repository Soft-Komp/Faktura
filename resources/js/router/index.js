import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    {
        path: '/',
        redirect: '/dashboard'
    },
    {
        path: '/login',
        name: 'Login',
        component: () => import('../views/Auth/Login.vue'),
        meta: { guest: true }
    },
    {
        path: '/register',
        name: 'Register', 
        component: () => import('../views/Auth/Register.vue'),
        meta: { guest: true }
    },
    {
        path: '/dashboard',
        name: 'Dashboard',
        component: () => import('../views/Dashboard.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/firmy',
        name: 'Firmy',
        component: () => import('../views/Firmy/Index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/odbiorcy',
        name: 'Odbiorcy',
        component: () => import('../views/Odbiorcy/Index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/artykuly',
        name: 'Artykuly',
        component: () => import('../views/Artykuly/Index.vue'),
        meta: { requiresAuth: true }
    },
    {
        path: '/dokumenty',
        name: 'Dokumenty',
        component: () => import('../views/Dokumenty/Index.vue'),
        meta: { requiresAuth: true }
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

// Navigation guards
router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();
    
    if (to.meta.requiresAuth && !authStore.isAuthenticated) {
        next('/login');
    } else if (to.meta.guest && authStore.isAuthenticated) {
        next('/dashboard');
    } else {
        next();
    }
});

export default router;