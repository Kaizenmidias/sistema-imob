#!/usr/bin/env bash

set -Eeuo pipefail

APP_ROOT="${APP_ROOT:-/www/wwwroot/meteorikahimob.com.br}"
APP_OWNER="${APP_OWNER:-lucas}"
WEB_USER="${WEB_USER:-www}"
WEB_GROUP="${WEB_GROUP:-www}"
PHP_BIN="${PHP_BIN:-php}"
SKIP_SERVICE_RESTART="${SKIP_SERVICE_RESTART:-0}"
SKIP_GROUP_SYNC="${SKIP_GROUP_SYNC:-0}"

log() {
  printf '[%s] %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*"
}

warn() {
  printf '[%s] WARNING: %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*" >&2
}

fail() {
  printf '[%s] ERROR: %s\n' "$(date '+%Y-%m-%d %H:%M:%S')" "$*" >&2
  exit 1
}

run_privileged() {
  if [[ "$(id -u)" -eq 0 ]]; then
    "$@"
    return
  fi

  sudo "$@"
}

run_as_owner() {
  if [[ "$(id -un)" == "$APP_OWNER" ]]; then
    "$@"
    return
  fi

  if [[ "$(id -u)" -eq 0 ]]; then
    sudo -u "$APP_OWNER" -H "$@"
    return
  fi

  sudo -u "$APP_OWNER" -H "$@"
}

run_artisan() {
  local command=("$PHP_BIN" artisan "$@")

  (
    cd "$APP_ROOT"
    run_as_owner "${command[@]}"
  )
}

require_paths() {
  [[ -d "$APP_ROOT" ]] || fail "Projeto nao encontrado em $APP_ROOT"
  [[ -f "$APP_ROOT/artisan" ]] || fail "Arquivo artisan nao encontrado em $APP_ROOT"
}

ensure_base_directories() {
  log "Garantindo diretorios criticos do Laravel"

  run_privileged mkdir -p \
    "$APP_ROOT/storage/framework/cache/data" \
    "$APP_ROOT/storage/framework/sessions" \
    "$APP_ROOT/storage/framework/views" \
    "$APP_ROOT/storage/logs" \
    "$APP_ROOT/storage/app/public" \
    "$APP_ROOT/storage/app/private" \
    "$APP_ROOT/bootstrap/cache"
}

sync_group_membership() {
  if [[ "$SKIP_GROUP_SYNC" == "1" ]]; then
    log "Sincronizacao de grupos ignorada por configuracao"
    return
  fi

  if ! id "$APP_OWNER" >/dev/null 2>&1; then
    fail "Usuario dono do projeto nao existe: $APP_OWNER"
  fi

  if ! getent group "$WEB_GROUP" >/dev/null 2>&1; then
    fail "Grupo do webserver nao existe: $WEB_GROUP"
  fi

  if id -nG "$APP_OWNER" | tr ' ' '\n' | grep -qx "$WEB_GROUP"; then
    log "Usuario $APP_OWNER ja pertence ao grupo $WEB_GROUP"
    return
  fi

  log "Adicionando $APP_OWNER ao grupo $WEB_GROUP para deploys e builds locais"
  run_privileged usermod -a -G "$WEB_GROUP" "$APP_OWNER"
  warn "Pode ser necessario sair e entrar novamente na sessao do usuario $APP_OWNER para aplicar o novo grupo."
}

apply_ownership() {
  log "Aplicando ownership $APP_OWNER:$WEB_GROUP no projeto"
  run_privileged chown -R "$APP_OWNER:$WEB_GROUP" "$APP_ROOT"
}

apply_default_permissions() {
  log "Aplicando permissoes padrao seguras"
  run_privileged find "$APP_ROOT" \
    -path "$APP_ROOT/.git" -prune -o \
    -path "$APP_ROOT/node_modules" -prune -o \
    -path "$APP_ROOT/vendor" -prune -o \
    -type d -exec chmod 755 {} +

  run_privileged find "$APP_ROOT" \
    -path "$APP_ROOT/.git" -prune -o \
    -path "$APP_ROOT/node_modules" -prune -o \
    -path "$APP_ROOT/vendor" -prune -o \
    -type f -exec chmod 644 {} +

  run_privileged chmod 755 "$APP_ROOT/artisan" "$APP_ROOT/deploy-permissions.sh"
}

apply_writable_permissions() {
  log "Aplicando permissoes de escrita nos diretorios criticos"

  local writable_dirs=(
    "$APP_ROOT/storage"
    "$APP_ROOT/storage/app"
    "$APP_ROOT/storage/app/public"
    "$APP_ROOT/storage/framework"
    "$APP_ROOT/storage/framework/cache"
    "$APP_ROOT/storage/framework/cache/data"
    "$APP_ROOT/storage/framework/sessions"
    "$APP_ROOT/storage/framework/views"
    "$APP_ROOT/storage/logs"
    "$APP_ROOT/bootstrap/cache"
  )

  run_privileged find "${writable_dirs[@]}" -type d -exec chmod 2775 {} +
  run_privileged find "${writable_dirs[@]}" -type f -exec chmod 664 {} +
}

cleanup_runtime_cache() {
  log "Limpando caches e artefatos de runtime sem remover .gitignore"

  run_privileged find "$APP_ROOT/bootstrap/cache" -mindepth 1 ! -name '.gitignore' -delete
  run_privileged find "$APP_ROOT/storage/framework/views" -mindepth 1 ! -name '.gitignore' -delete
  run_privileged find "$APP_ROOT/storage/framework/cache" -mindepth 1 \
    ! -path "$APP_ROOT/storage/framework/cache/.gitignore" \
    ! -path "$APP_ROOT/storage/framework/cache/data" \
    -delete
  run_privileged find "$APP_ROOT/storage/framework/cache/data" -mindepth 1 ! -name '.gitignore' -delete
  run_privileged find "$APP_ROOT/storage/framework/sessions" -mindepth 1 ! -name '.gitignore' -delete

  run_privileged touch "$APP_ROOT/storage/logs/laravel.log"
  run_privileged chown "$APP_OWNER:$WEB_GROUP" "$APP_ROOT/storage/logs/laravel.log"
  run_privileged chmod 664 "$APP_ROOT/storage/logs/laravel.log"
}

optimize_laravel() {
  log "Executando optimize:clear"
  run_artisan optimize:clear

  log "Garantindo storage:link"
  run_artisan storage:link || true

  log "Regerando manifestos de pacotes"
  run_artisan package:discover --ansi

  log "Regerando caches do Laravel"
  run_artisan config:cache
  run_artisan route:cache
  run_artisan view:cache
  run_artisan queue:restart || true
}

detect_php_fpm_user() {
  ps -eo user:32,group:32,args | awk '
    /php-fpm: pool|php-fpm master process|php-fpm: master process|lsphp/ {
      print $1 ":" $2
      exit
    }
  '
}

restart_service_if_exists() {
  local service_name="$1"

  if command -v systemctl >/dev/null 2>&1 && systemctl list-unit-files "$service_name" >/dev/null 2>&1; then
    log "Reiniciando servico $service_name"
    run_privileged systemctl restart "$service_name"
    return
  fi

  if command -v service >/dev/null 2>&1; then
    log "Tentando reiniciar servico $service_name via service"
    run_privileged service "${service_name%.service}" restart || true
  fi
}

restart_runtime_services() {
  if [[ "$SKIP_SERVICE_RESTART" == "1" ]]; then
    log "Reinicio de servicos ignorado por configuracao"
    return
  fi

  log "Validando usuario do PHP-FPM"
  local fpm_identity
  fpm_identity="$(detect_php_fpm_user || true)"
  if [[ -n "$fpm_identity" ]]; then
    log "PHP-FPM/LSAPI detectado como $fpm_identity"
  else
    warn "Nao foi possivel detectar o usuario do PHP-FPM automaticamente"
  fi

  restart_service_if_exists "nginx.service"

  local php_services=(
    "php8.3-fpm.service"
    "php-fpm-83.service"
    "php-fpm.service"
    "lsphp83.service"
  )

  local restarted_php=0
  for service_name in "${php_services[@]}"; do
    if command -v systemctl >/dev/null 2>&1 && systemctl list-unit-files "$service_name" >/dev/null 2>&1; then
      restart_service_if_exists "$service_name"
      restarted_php=1
      break
    fi
  done

  if [[ "$restarted_php" == "0" ]]; then
    warn "Nenhum servico PHP-FPM padrao foi detectado. Reinicie manualmente o pool configurado no aaPanel."
  fi

  if command -v supervisorctl >/dev/null 2>&1; then
    log "Reiniciando workers Laravel encontrados no Supervisor"
    local programs
    programs="$(run_privileged supervisorctl status 2>/dev/null | awk '/laravel|queue|worker/ {print $1}')"
    if [[ -n "$programs" ]]; then
      while IFS= read -r program; do
        [[ -n "$program" ]] || continue
        run_privileged supervisorctl restart "$program" || true
      done <<< "$programs"
    else
      warn "Nenhum worker Laravel foi encontrado no Supervisor"
    fi
  fi
}

print_summary() {
  cat <<EOF

Resumo da estrategia aplicada:
- owner do projeto: $APP_OWNER
- group do projeto: $WEB_GROUP
- diretorios padrao: 755
- arquivos padrao: 644
- storage e bootstrap/cache: 2775 nos diretorios e 664 nos arquivos
- setgid aplicado para herdar automaticamente o grupo $WEB_GROUP
- caches limpos e recompilados com o usuario $APP_OWNER

EOF
}

main() {
  require_paths
  ensure_base_directories
  sync_group_membership
  apply_ownership
  apply_default_permissions
  apply_writable_permissions
  cleanup_runtime_cache
  optimize_laravel
  restart_runtime_services
  print_summary
}

main "$@"
