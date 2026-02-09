import { ChangeEvent, FormEvent, useEffect, useState } from 'react';
import { useAuth } from '@/features/auth/AuthProvider';
import { http } from '@/shared/api/http';
import type { StudentUser } from '@/shared/types/auth';
import { Button } from '@/shared/ui/Button';
import { Card } from '@/shared/ui/Card';
import { Input } from '@/shared/ui/Input';

const ALLOWED_TYPES = ['image/jpeg', 'image/png', 'image/webp'];
const MAX_FILE_SIZE_BYTES = 1024 * 1024;

export function StudentOnboardingPage() {
  const { session, refreshSession } = useAuth();
  const studentUser = session?.role === 'student' ? (session.user as StudentUser) : null;

  const [loading, setLoading] = useState(false);
  const [message, setMessage] = useState('');
  const [error, setError] = useState('');

  const [uploadingPhoto, setUploadingPhoto] = useState(false);
  const [photoMessage, setPhotoMessage] = useState('');
  const [photoError, setPhotoError] = useState('');
  const [photoFile, setPhotoFile] = useState<File | null>(null);
  const [photoPreview, setPhotoPreview] = useState<string | null>(
    studentUser?.profile_photo_url ?? null
  );

  useEffect(() => {
    if (studentUser && !photoFile) {
      setPhotoPreview(studentUser.profile_photo_url ?? null);
    }
  }, [photoFile, studentUser]);

  useEffect(() => {
    return () => {
      if (photoPreview?.startsWith('blob:')) {
        URL.revokeObjectURL(photoPreview);
      }
    };
  }, [photoPreview]);

  function onPhotoSelected(event: ChangeEvent<HTMLInputElement>) {
    const file = event.target.files?.[0] ?? null;

    setPhotoError('');
    setPhotoMessage('');

    if (!file) {
      setPhotoFile(null);
      return;
    }

    if (!ALLOWED_TYPES.includes(file.type)) {
      setPhotoFile(null);
      setPhotoError('Formato invalido. Solo JPG, PNG o WEBP.');
      event.target.value = '';
      return;
    }

    if (file.size > MAX_FILE_SIZE_BYTES) {
      setPhotoFile(null);
      setPhotoError('La imagen excede 1MB.');
      event.target.value = '';
      return;
    }

    if (photoPreview?.startsWith('blob:')) {
      URL.revokeObjectURL(photoPreview);
    }

    setPhotoFile(file);
    setPhotoPreview(URL.createObjectURL(file));
  }

  async function uploadPhoto() {
    if (!photoFile) {
      setPhotoError('Selecciona una imagen primero.');
      return;
    }

    setUploadingPhoto(true);
    setPhotoMessage('');
    setPhotoError('');

    const formData = new FormData();
    formData.append('photo', photoFile);

    try {
      const response = await http.post<{ data: { profile_photo_url: string } }>('/student/profile-photo', formData, {
        headers: {
          'Content-Type': 'multipart/form-data',
        },
      });

      setPhotoPreview(response.data.data.profile_photo_url);
      setPhotoFile(null);
      setPhotoMessage('Foto de perfil actualizada.');
      await refreshSession();
    } catch {
      setPhotoError('No fue posible subir la foto.');
    } finally {
      setUploadingPhoto(false);
    }
  }

  async function onSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setLoading(true);
    setMessage('');
    setError('');

    const formData = new FormData(event.currentTarget);

    try {
      await http.put('/student/onboarding', {
        local_phone: String(formData.get('local_phone') ?? ''),
        movil_phone: String(formData.get('movil_phone') ?? ''),
        birth_date: String(formData.get('birth_date') ?? ''),
        actual_address: String(formData.get('actual_address') ?? ''),
        city: String(formData.get('city') ?? ''),
        actual_state: String(formData.get('actual_state') ?? ''),
        occupation: String(formData.get('occupation') ?? ''),
        curp: String(formData.get('curp') ?? ''),
        emergency_contact: String(formData.get('emergency_contact') ?? ''),
      });
      setMessage('Perfil completado correctamente.');
      await refreshSession();
    } catch {
      setError('No fue posible actualizar el perfil.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <Card>
      <h1 className="text-lg font-bold text-slate-900">Completar perfil</h1>
      <p className="mt-1 text-sm text-slate-600">Este paso es obligatorio para usar todo el dashboard.</p>

      <div className="mt-5 rounded-xl border border-slate-200 p-4">
        <p className="text-sm font-semibold text-slate-800">Foto de perfil</p>
        <p className="mt-1 text-xs text-slate-500">Formatos: JPG, PNG, WEBP. Tamano maximo: 1MB.</p>

        <div className="mt-3 flex flex-wrap items-center gap-4">
          {photoPreview ? (
            <img
              src={photoPreview}
              alt="Vista previa de foto"
              className="h-20 w-20 rounded-full border border-slate-200 object-cover"
            />
          ) : (
            <div className="flex h-20 w-20 items-center justify-center rounded-full border border-dashed border-slate-300 text-xs text-slate-500">
              Sin foto
            </div>
          )}

          <div className="flex flex-wrap items-center gap-2">
            <Input type="file" accept="image/png,image/jpeg,image/webp" onChange={onPhotoSelected} />
            <Button type="button" onClick={uploadPhoto} disabled={uploadingPhoto || !photoFile}>
              {uploadingPhoto ? 'Subiendo...' : 'Subir foto'}
            </Button>
          </div>
        </div>

        {photoMessage && <p className="mt-2 text-sm text-emerald-700">{photoMessage}</p>}
        {photoError && <p className="mt-2 text-sm text-rose-700">{photoError}</p>}
      </div>

      <form className="mt-6 grid gap-4 md:grid-cols-2" onSubmit={onSubmit}>
        <label className="grid gap-1 text-sm">
          Telefono local
          <Input name="local_phone" />
        </label>
        <label className="grid gap-1 text-sm">
          Telefono movil
          <Input name="movil_phone" required />
        </label>
        <label className="grid gap-1 text-sm">
          Fecha de nacimiento
          <Input name="birth_date" type="date" required />
        </label>
        <label className="grid gap-1 text-sm md:col-span-2">
          Direccion
          <Input name="actual_address" required />
        </label>
        <label className="grid gap-1 text-sm">
          Ciudad
          <Input name="city" required />
        </label>
        <label className="grid gap-1 text-sm">
          Estado
          <Input name="actual_state" required />
        </label>
        <label className="grid gap-1 text-sm">
          Ocupacion
          <Input name="occupation" />
        </label>
        <label className="grid gap-1 text-sm">
          CURP
          <Input name="curp" />
        </label>
        <label className="grid gap-1 text-sm md:col-span-2">
          Contacto de emergencia
          <Input name="emergency_contact" />
        </label>
        <div className="md:col-span-2 flex items-center gap-3">
          <Button type="submit" disabled={loading}>{loading ? 'Guardando...' : 'Guardar perfil'}</Button>
          {message && <span className="text-sm text-emerald-700">{message}</span>}
          {error && <span className="text-sm text-rose-700">{error}</span>}
        </div>
      </form>
    </Card>
  );
}
