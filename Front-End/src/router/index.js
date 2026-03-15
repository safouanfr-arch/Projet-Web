import { createRouter, createWebHistory } from 'vue-router'

// Routes: map '/' to Accueil page. Add more routes here later.
const routes = [
	{
		path: '/',
		name: 'Accueil',
		component: () => import('../page/Accueil.vue')
	},
	{
		path: '/Favoris',
		name: 'Favoris',
		component: () => import('../page/Favoris.vue')
	},

	{
		path: '/Articles',
		name: 'Articles',
		component: () => import('../page/Articles.vue')
	},

	{
		path: '/Formulaire',
		name: 'Formulaire',
		component: () => import('../page/Formulaire.vue')
	},


	{
		path: '/Connexion',
		name: 'Connexion',
		component: () => import('../page/Connexion.vue')
	},

	{
		path: '/Apropos',
		name: 'Apropos',
		component: () => import('../page/Apropos.vue')
	},

	{
		path: '/article/:id',
		name: 'ArticleDetail',
		component: () => import('../page/ArticleDetail.vue')
	},

	

	// fallback - redirect unknown paths to home
	{
		path: '/:catchAll(.*)',
		redirect: '/'
	}
]

const router = createRouter({
	history: createWebHistory(),
	routes
})

export default router
