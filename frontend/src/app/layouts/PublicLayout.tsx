import { Link, NavLink, Outlet } from 'react-router-dom';
import { BrandLogo } from '@/shared/ui/BrandLogo';

export function PublicLayout() {
  const navBase = 'rounded-md px-3 py-1.5 transition';
  const navActive = 'bg-brand-100 text-brand-900';
  const navInactive = 'text-brand-800 hover:bg-brand-50';

  return (
    <div className="min-h-screen bg-gradient-to-b from-brand-50 via-white to-slate-100">
      <header className="border-b border-brand-100 bg-white/90 backdrop-blur">
        <div className="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
          <Link to="/" className="inline-flex items-center">
            <BrandLogo className="h-12" />
          </Link>
          <nav className="flex items-center gap-4 text-sm font-semibold text-brand-800">
            <NavLink to="/register" className={({ isActive }) => `${navBase} ${isActive ? navActive : navInactive}`}>
              Registro
            </NavLink>
            <NavLink to="/login" className={({ isActive }) => `${navBase} ${isActive ? navActive : navInactive}`}>
              Login
            </NavLink>
          </nav>
        </div>
      </header>
      <main className="mx-auto max-w-6xl px-4 py-8">
        <Outlet />
      </main>
    </div>
  );
}
