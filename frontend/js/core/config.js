const APP_BASE_PATH =
  typeof window !== "undefined" && window.location
    ? (() => {
        const path = window.location.pathname;
        if (path.includes("/overgrace-main")) return "/overgrace-main";
        if (path.includes("/overgrace")) return "/overgrace";
        return "";
      })()
    : "";

export const BASE_URL = `${APP_BASE_PATH}/api`;

export function getHeaders(isFormData = false) {
  const token =
    localStorage.getItem("token") || // token adm
    localStorage.getItem("token_client"); // token client

  const headers = {};

  if (token) {
    headers.Authorization = `Bearer ${token}`;
  }

  if (!isFormData) {
    headers["Content-Type"] = "application/json";
  }

  return headers;
}
