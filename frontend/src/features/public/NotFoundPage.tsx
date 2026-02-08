import { Link } from 'react-router-dom';
import { Card } from '@/shared/ui/Card';

export function NotFoundPage() {
  return (
    <Card>
      <h1 className="text-xl font-bold text-slate-900">Ruta no encontrada</h1>
      <p className="mt-2 text-sm text-slate-600">La pagina solicitada no existe en este frontend.</p>
      <Link to="/" className="mt-4 inline-block text-sm font-semibold">
        Volver al inicio
      </Link>
    </Card>
  );
}
