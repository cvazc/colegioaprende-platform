import { Link, NavLink, Outlet } from 'react-router-dom';

export function PublicLayout() {
  return (
    <div className="min-h-screen bg-gradient-to-b from-brand-50 via-white to-slate-100">
      <header className="border-b border-slate-200 bg-white/80 backdrop-blur">
        <div className="mx-auto flex max-w-6xl items-center justify-between px-4 py-3">
          <Link to="/" className="text-lg font-extrabold text-brand-800">
            Colegio Aprende
          </Link>
          <nav className="flex items-center gap-4 text-sm font-medium text-slate-600">
            <NavLink to="/register">Registro</NavLink>
            <NavLink to="/login">Login</NavLink>
          </nav>
        </div>
      </header>
      <main className="mx-auto max-w-6xl px-4 py-8">
        <Outlet />
      </main>
    </div>
  );
}
