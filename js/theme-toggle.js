// js/theme.js - SISTEMA DE TEMA PARA TODAS AS PÁGINAS

// ===== SISTEMA DE TEMA CLARO/ESCURO =====

// Elementos DOM
const themeToggle = document.getElementById('themeToggle');
const body = document.body;

// Verificar preferência salva ou preferência do sistema
function getThemePreference() {
    // Verificar se há uma preferência salva no localStorage
    const savedTheme = localStorage.getItem('theme');
    if (savedTheme) {
        return savedTheme;
    }
    
    // Verificar preferência do sistema
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
    }
    
    // Tema claro como padrão
    return 'light';
}

// Aplicar tema
function applyTheme(theme) {
    if (theme === 'dark') {
        body.setAttribute('data-theme', 'dark');
    } else {
        body.removeAttribute('data-theme');
    }
}

// Alternar tema
function toggleTheme() {
    const currentTheme = body.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    applyTheme(newTheme);
    localStorage.setItem('theme', newTheme);
}

// Inicializar tema
function initTheme() {
    const theme = getThemePreference();
    applyTheme(theme);
    
    // Adicionar evento de clique no botão de alternância (se existir)
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
    }
    
    // Ouvir mudanças na preferência do sistema
    if (window.matchMedia) {
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            // Só aplicar se o usuário não tiver uma preferência salva
            if (!localStorage.getItem('theme')) {
                applyTheme(e.matches ? 'dark' : 'light');
            }
        });
    }
}

// Inicializar quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', initTheme);