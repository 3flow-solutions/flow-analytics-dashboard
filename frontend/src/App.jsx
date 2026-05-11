import React, { useState, useEffect } from 'react';
import Dashboard from './pages/Dashboard';
import Login from './pages/Login';
import { getAuth, isAuthenticated } from './services/auth';

function App() {
  const [authenticated, setAuthenticated] = useState(false);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Check if user is authenticated
    if (isAuthenticated()) {
      setAuthenticated(true);
    }
    setLoading(false);
  }, []);

  if (loading) {
    return (
      <div className="flex items-center justify-center h-screen bg-white">
        <div className="text-center">
          <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-red-900 mx-auto mb-4"></div>
          <p className="text-gray-600">Loading...</p>
        </div>
      </div>
    );
  }

  return (
    <div className="bg-white min-h-screen">
      {authenticated ? (
        <Dashboard onLogout={() => setAuthenticated(false)} />
      ) : (
        <Login onLogin={() => setAuthenticated(true)} />
      )}
    </div>
  );
}

export default App;
