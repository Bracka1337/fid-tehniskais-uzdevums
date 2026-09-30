import { useEffect, useMemo, useState } from 'react'
import { logout } from '../api/auth'
import { fetchDocuments } from '../api/documents'
import { DocumentFilters } from '../components/DocumentFilters'
import { DocumentTable } from '../components/DocumentTable'
import type {
  Document,
  DocumentFilters as Filters,
  User,
} from '../types/document'

type Props = {
  user: User
  onLogout: () => void
}

const PAGE_SIZE = 15

const initialFilters: Filters = {
  search: '',
  importance: '',
  category: '',
  is_active: '',
  sort: 'created_on',
  direction: 'desc',
}

export function DocumentsPage({ onLogout }: Props) {
  const [filters, setFilters] = useState<Filters>(initialFilters)
  const [debouncedSearch, setDebouncedSearch] = useState('')
  const [documents, setDocuments] = useState<Document[]>([])
  const [page, setPage] = useState(1)
  const [loading, setLoading] = useState(true)
  const [hasLoaded, setHasLoaded] = useState(false)
  const [error, setError] = useState<string | null>(null)
  const [loggingOut, setLoggingOut] = useState(false)

  const pageCount = Math.max(1, Math.ceil(documents.length / PAGE_SIZE))

  const pageDocuments = useMemo(() => {
    const start = (page - 1) * PAGE_SIZE
    return documents.slice(start, start + PAGE_SIZE)
  }, [documents, page])

  const emptyMessage = !hasLoaded
    ? 'Ielādē dokumentus…'
    : error
      ? error
      : 'Dokumenti nav atrasti.'

  useEffect(() => {
    const timer = window.setTimeout(() => {
      setDebouncedSearch(filters.search)
    }, 300)

    return () => window.clearTimeout(timer)
  }, [filters.search])

  useEffect(() => {
    setPage(1)
  }, [
    debouncedSearch,
    filters.importance,
    filters.category,
    filters.is_active,
    filters.sort,
    filters.direction,
  ])

  useEffect(() => {
    let cancelled = false

    const load = async () => {
      setLoading(true)
      setError(null)

      try {
        const response = await fetchDocuments({
          ...filters,
          search: debouncedSearch,
        })

        if (!cancelled) {
          setDocuments(response.data)
          setHasLoaded(true)
        }
      } catch (err) {
        if (!cancelled) {
          setDocuments([])
          setHasLoaded(true)
          setError(
            err instanceof Error ? err.message : 'Neizdevās ielādēt dokumentus',
          )
        }
      } finally {
        if (!cancelled) {
          setLoading(false)
        }
      }
    }

    void load()

    return () => {
      cancelled = true
    }
  }, [
    debouncedSearch,
    filters.importance,
    filters.category,
    filters.is_active,
    filters.sort,
    filters.direction,
  ])

  useEffect(() => {
    if (page > pageCount) {
      setPage(pageCount)
    }
  }, [page, pageCount])

  useEffect(() => {
    window.scrollTo({ top: 0, behavior: 'smooth' })
  }, [page])

  const handleSort = (column: string) => {
    setFilters((current) => {
      if (current.sort === column) {
        return {
          ...current,
          direction: current.direction === 'asc' ? 'desc' : 'asc',
        }
      }

      return {
        ...current,
        sort: column,
        direction: 'asc',
      }
    })
  }

  const handleLogout = async () => {
    setLoggingOut(true)

    try {
      await logout()
      onLogout()
    } catch {
      onLogout()
    } finally {
      setLoggingOut(false)
    }
  }

  const handlePreviousPage = () => {
    setPage((current) => Math.max(1, current - 1))
  }

  const handleNextPage = () => {
    setPage((current) => Math.min(pageCount, current + 1))
  }

  return (
    <div className="documents">
      <header className="documents__header">
        <div>
          <p className="documents__brand">Dokumentu katalogs</p>
          <h1 className="documents__title">Dokumenti</h1>
        </div>
        <button
          type="button"
          className="btn btn--danger"
          onClick={() => void handleLogout()}
          disabled={loggingOut}
        >
          {loggingOut ? 'Izrakstās…' : 'Iziet'}
        </button>
      </header>

      <DocumentFilters filters={filters} onChange={setFilters} />

      <DocumentTable
        documents={pageDocuments}
        sort={filters.sort}
        direction={filters.direction}
        onSort={handleSort}
        loading={loading}
        emptyMessage={emptyMessage}
      />

      {documents.length > 0 ? (
        <div className="documents__pagination">
          <button
            type="button"
            className="btn"
            disabled={page <= 1}
            onClick={handlePreviousPage}
          >
            Iepriekšējā
          </button>
          <span>
            Lapa {page} no {pageCount}
          </span>
          <button
            type="button"
            className="btn"
            disabled={page >= pageCount}
            onClick={handleNextPage}
          >
            Nākamā
          </button>
        </div>
      ) : null}
    </div>
  )
}
