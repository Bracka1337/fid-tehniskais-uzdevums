import { useEffect, useState } from 'react'
import { fetchMe } from './api/auth'
import { ensureCsrfCookie } from './api/client'
import { DocumentsPage } from './pages/DocumentsPage'
import { LoginPage } from './pages/LoginPage'
import type { User } from './types/document'

function App() {
  const [user, setUser] = useState<User | null>(null)
  const [booting, setBooting] = useState(true)

  useEffect(() => {
    let cancelled = false

    const boot = async () => {
      try {
        await ensureCsrfCookie()
        const me = await fetchMe()

        if (!cancelled) {
          setUser(me)
        }
      } catch {
        if (!cancelled) {
          setUser(null)
        }
      } finally {
        if (!cancelled) {
          setBooting(false)
        }
      }
    }

    void boot()

    return () => {
      cancelled = true
    }
  }, [])

  if (booting) {
    return <div className="boot">Ielādē…</div>
  }

  if (!user) {
    return <LoginPage onSuccess={setUser} />
  }

  return <DocumentsPage user={user} onLogout={() => setUser(null)} />
}

export default App
