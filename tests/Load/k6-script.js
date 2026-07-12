// k6 Load Testing Script
// Usage: k6 run tests/Load/k6-script.js
// Install k6: https://k6.io/docs/getting-started/installation/

import http from 'k6/http';
import { check, sleep, group } from 'k6';
import { Rate, Trend } from 'k6/metrics';

const BASE_URL = __ENV.BASE_URL || 'http://localhost:8000';

const loginFailureRate = new Rate('login_failures');
const dashboardDuration = new Trend('dashboard_response_time');
const apiDuration = new Trend('api_response_time');

export const options = {
  stages: [
    { duration: '30s', target: 10 },  // Ramp-up
    { duration: '1m', target: 20 },   // Steady
    { duration: '30s', target: 0 },   // Ramp-down
  ],
  thresholds: {
    http_req_duration: ['p(95)<2000'], // 95% of requests under 2s
    login_failures: ['rate<0.1'],      // Less than 10% failure
  },
};

export default function () {
  // 1. Health check
  group('health', () => {
    const res = http.get(`${BASE_URL}/api/health`);
    check(res, { 'health status is 200': (r) => r.status === 200 });
  });

  // 2. Login
  group('login', () => {
    const payload = JSON.stringify({
      email: `user_${__VU}@example.com`,
      password: 'password',
    });
    const res = http.post(`${BASE_URL}/api/auth/login`, payload, {
      headers: { 'Content-Type': 'application/json' },
    });
    loginFailureRate.add(res.status !== 200);
    check(res, { 'login success': (r) => r.status === 200 });
    if (res.status === 200) {
      const token = res.json('token');
      const headers = {
        Authorization: `Bearer ${token}`,
        'Content-Type': 'application/json',
      };

      // 3. List workspaces
      group('workspaces', () => {
        const wsRes = http.get(`${BASE_URL}/api/workspaces`, { headers });
        check(wsRes, { 'workspaces listed': (r) => r.status === 200 });
      });

      // 4. Dashboard data
      group('dashboard', () => {
        const start = Date.now();
        const dashRes = http.get(`${BASE_URL}/api/workspace/dashboard`, { headers });
        dashboardDuration.add(Date.now() - start);
        check(dashRes, { 'dashboard loaded': (r) => r.status === 200 });
      });

      // 5. Financial data (incomes, expenses)
      group('financial', () => {
        const incRes = http.get(`${BASE_URL}/api/workspace/incomes`, { headers });
        check(incRes, { 'incomes loaded': (r) => r.status === 200 });

        const expRes = http.get(`${BASE_URL}/api/workspace/expenses`, { headers });
        check(expRes, { 'expenses loaded': (r) => r.status === 200 });
      });
    }
  });

  sleep(1);
}
