import { createRouter, createWebHistory } from 'vue-router'
import { getActivePinia } from 'pinia'

import HomeIndex from '@/views/home/Index.vue'
import ProjectIndex from '@/views/projects/Index.vue'
import ProjectForm from '@/views/projects/Form.vue'
import NewsIndex from '@/views/news/Index.vue'
import NewsForm from '@/views/news/Form.vue'
import PageIndex from '@/views/pages/Index.vue'
import PageForm from '@/views/pages/Form.vue'
import TeamIndex from '@/views/team/Index.vue'
import TeamForm from '@/views/team/Form.vue'
import JobIndex from '@/views/jobs/Index.vue'
import JobForm from '@/views/jobs/Form.vue'
import BookIndex from '@/views/books/Index.vue'
import BookForm from '@/views/books/Form.vue'
import EntryIndex from '@/views/entries/Index.vue'
import EntryForm from '@/views/entries/Form.vue'
import CategoryIndex from '@/views/settings/Categories.vue'
import CategoryForm from '@/views/settings/CategoryForm.vue'
import TypeForm from '@/views/settings/TypeForm.vue'
import UserIndex from '@/views/users/Index.vue'
import UserForm from '@/views/users/Form.vue'

/**
 * index / create / edit routes for a resource.
 */
function resource(name, path, Index, Form, titles, meta = {}) {
	return [
		{ path: `/dashboard/${path}`, name: `${name}.index`, component: Index, meta: { title: titles[0], ...meta } },
		{ path: `/dashboard/${path}/create`, name: `${name}.create`, component: Form, meta: { title: titles[1], ...meta } },
		{ path: `/dashboard/${path}/:id/edit`, name: `${name}.edit`, component: Form, meta: { title: titles[2], ...meta } },
	]
}

const entry = (type, path, section) =>
	resource(type, path, EntryIndex, EntryForm, [section, 'Neuer Eintrag', 'Eintrag bearbeiten'], { entryType: type, section })

const routes = [
	{ path: '/dashboard', redirect: { name: 'home' } },
	{ path: '/dashboard/home', name: 'home', component: HomeIndex, meta: { title: 'Startseite' } },
	...resource('projects', 'projects', ProjectIndex, ProjectForm, ['Projekte', 'Neues Projekt', 'Projekt bearbeiten']),
	...resource('news', 'news', NewsIndex, NewsForm, ['News', 'Neue News', 'News bearbeiten']),
	...resource('pages', 'pages', PageIndex, PageForm, ['Seiten', 'Neue Seite', 'Seite bearbeiten']),
	...resource('team', 'team', TeamIndex, TeamForm, ['Team', 'Neues Mitglied', 'Mitglied bearbeiten']),
	...resource('jobs', 'jobs', JobIndex, JobForm, ['Jobs', 'Neues Inserat', 'Inserat bearbeiten']),
	...resource('books', 'books', BookIndex, BookForm, ['Bücher', 'Neues Buch', 'Buch bearbeiten']),
	...entry('press', 'press', 'Presse'),
	...entry('award', 'awards', 'Auszeichnungen'),
	...entry('lecture', 'lectures', 'Vorträge'),
	...resource('categories', 'categories', CategoryIndex, CategoryForm, ['Kategorien', 'Neue Kategorie', 'Kategorie bearbeiten']),
	{ path: '/dashboard/types/create', name: 'types.create', component: TypeForm, meta: { title: 'Neuer Typ' } },
	{ path: '/dashboard/types/:id/edit', name: 'types.edit', component: TypeForm, meta: { title: 'Typ bearbeiten' } },
	...resource('users', 'users', UserIndex, UserForm, ['Benutzer', 'Neuer Benutzer', 'Benutzer bearbeiten']),
]

const router = createRouter({
	history: createWebHistory(),
	routes,
})

router.beforeEach(() => {
  const pinia = getActivePinia()
  if (pinia) {
    Object.values(pinia.state.value).forEach((storeState) => {
      if ('errors' in storeState) {
        storeState.errors = {}
      }
    })
  }
})

router.afterEach((to) => {
  const appName = document.title.split('–').pop()?.trim() || 'CMS'
  document.title = to.meta.title
    ? `${to.meta.title} – ${appName}`
    : appName
})

export default router
