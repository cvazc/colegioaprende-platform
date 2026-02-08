import { http } from '@/shared/api/http';
import type { AuthSession, LoginPayload } from '@/shared/types/auth';

type LoginResponse = {
  token: string;
  role: 'employee' | 'student';
  user: AuthSession['user'];
};

type MeResponse = {
  data: {
    role: 'employee' | 'student' | 'unknown';
    user: AuthSession['user'];
  };
};

export async function loginRequest(payload: LoginPayload): Promise<AuthSession> {
  const { data } = await http.post<LoginResponse>('/auth/login', payload);
  return {
    token: data.token,
    role: data.role,
    user: data.user,
  };
}

export async function meRequest(): Promise<MeResponse['data']> {
  const { data } = await http.get<MeResponse>('/auth/me');
  return data.data;
}

export async function logoutRequest(): Promise<void> {
  await http.post('/auth/logout');
}
