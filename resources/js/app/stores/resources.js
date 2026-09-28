import { defineResourceStore } from './resource'
import {
	projectsApi, categoriesApi, categoryTypesApi, pagesApi, newsApi, teamApi, jobsApi, booksApi, entriesApi,
} from '@/api/resource'

export const useProjectStore = defineResourceStore('projects', projectsApi)
export const useCategoryStore = defineResourceStore('categories', categoriesApi)
export const useCategoryTypeStore = defineResourceStore('categoryTypes', categoryTypesApi)
export const usePageStore = defineResourceStore('pages', pagesApi)
export const useNewsStore = defineResourceStore('news', newsApi)
export const useTeamStore = defineResourceStore('team', teamApi)
export const useJobStore = defineResourceStore('jobs', jobsApi)
export const useBookStore = defineResourceStore('books', booksApi)
export const useEntryStore = defineResourceStore('entries', entriesApi)
