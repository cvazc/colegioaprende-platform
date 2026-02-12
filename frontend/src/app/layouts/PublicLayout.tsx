import { Link, Outlet } from 'react-router-dom';
import { useEffect, useState } from 'react';
import { BrandLogo } from '@/shared/ui/BrandLogo';

const menuItems = [
  { label: 'Inicio', href: '/#inicio' },
  { label: 'Programas', href: '/#programas' },
  { label: 'Como funciona', href: '/#como-funciona' },
  { label: 'Testimonios', href: '/#testimonios' },
  { label: 'FAQs', href: '/#faqs' },
  { label: 'Contacto', href: '/#contacto' },
];

function MoonIcon() {
  return (
    <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
      <path d="M21 12.79A9 9 0 1111.21 3c.5 2.72 2.64 4.86 5.36 5.36A9 9 0 0021 12.79z" />
    </svg>
  );
}

function SunIcon() {
  return (
    <svg viewBox="0 0 24 24" className="h-5 w-5" fill="none" stroke="currentColor" strokeWidth="2">
      <circle cx="12" cy="12" r="4" />
      <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" />
    </svg>
  );
}

function MenuIcon({ open }: { open: boolean }) {
  return (
    <svg viewBox="0 0 24 24" className="h-6 w-6" fill="none" stroke="currentColor" strokeWidth="2">
      {open ? <path d="M6 6l12 12M18 6L6 18" /> : <path d="M3 6h18M3 12h18M3 18h18" />}
    </svg>
  );
}

export function PublicLayout() {
  const [menuOpen, setMenuOpen] = useState(false);
  const [darkMode, setDarkMode] = useState(false);

  useEffect(() => {
    const stored = localStorage.getItem('colegioaprende.theme');
    if (stored) {
      setDarkMode(stored === 'dark');
      return;
    }

    setDarkMode(window.matchMedia('(prefers-color-scheme: dark)').matches);
  }, []);

  useEffect(() => {
    document.documentElement.classList.toggle('dark', darkMode);
    localStorage.setItem('colegioaprende.theme', darkMode ? 'dark' : 'light');
  }, [darkMode]);

  const toggleTheme = () => setDarkMode((value) => !value);

  return (
    <div className="min-h-screen bg-gradient-to-b from-brand-50 via-white to-slate-100 dark:from-slate-950 dark:via-slate-900 dark:to-slate-950">
      <header className="sticky top-0 z-40 border-b border-brand-100 bg-white/90 backdrop-blur dark:border-slate-700 dark:bg-slate-900/90">
        <div className="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
          <Link to="/" className="inline-flex items-center">
            <BrandLogo className="h-12" />
          </Link>

          <div className="hidden items-center gap-1 lg:flex">
            {menuItems.map((item) => (
              <a
                key={item.label}
                href={item.href}
                className="rounded-md px-3 py-1.5 text-sm font-semibold text-brand-800 transition hover:bg-brand-50 dark:text-accent-200 dark:hover:bg-slate-800"
              >
                {item.label}
              </a>
            ))}
            <Link
              to="/login"
              className="ml-2 rounded-lg border border-brand-200 px-3 py-1.5 text-sm font-semibold text-brand-800 hover:bg-brand-50 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800"
            >
              Iniciar sesion
            </Link>
            <button
              type="button"
              onClick={toggleTheme}
              className="ml-1 rounded-lg border border-brand-200 p-2 text-brand-800 hover:bg-brand-50 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800"
              aria-label="Cambiar modo oscuro"
            >
              {darkMode ? <SunIcon /> : <MoonIcon />}
            </button>
          </div>

          <div className="flex items-center gap-2 lg:hidden">
            <button
              type="button"
              onClick={toggleTheme}
              className="rounded-lg border border-brand-200 p-2 text-brand-800 hover:bg-brand-50 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800"
              aria-label="Cambiar modo oscuro"
            >
              {darkMode ? <SunIcon /> : <MoonIcon />}
            </button>
            <button
              type="button"
              onClick={() => setMenuOpen((value) => !value)}
              className="rounded-lg border border-brand-200 p-2 text-brand-800 hover:bg-brand-50 dark:border-slate-600 dark:text-slate-100 dark:hover:bg-slate-800"
              aria-label="Abrir menu"
            >
              <MenuIcon open={menuOpen} />
            </button>
          </div>
        </div>

        {menuOpen && (
          <div className="border-t border-brand-100 bg-white px-4 py-3 dark:border-slate-700 dark:bg-slate-900 lg:hidden">
            <nav className="grid gap-2">
              {menuItems.map((item) => (
                <a
                  key={item.label}
                  href={item.href}
                  onClick={() => setMenuOpen(false)}
                  className="rounded-md px-2 py-2 text-sm font-semibold text-brand-800 hover:bg-brand-50 dark:text-accent-200 dark:hover:bg-slate-800"
                >
                  {item.label}
                </a>
              ))}
              <Link
                to="/register"
                onClick={() => setMenuOpen(false)}
                className="rounded-md bg-brand-700 px-3 py-2 text-sm font-semibold text-white"
              >
                Aplicar ahora
              </Link>
              <Link
                to="/login"
                onClick={() => setMenuOpen(false)}
                className="rounded-md border border-brand-200 px-3 py-2 text-sm font-semibold text-brand-800 dark:border-slate-600 dark:text-slate-100"
              >
                Iniciar sesion
              </Link>
            </nav>
          </div>
        )}
      </header>

      <main className="mx-auto max-w-6xl px-4 py-8">
        <Outlet />
      </main>
    </div>
  );
}
