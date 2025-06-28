# start.ps1

Write-Host "`n🚀 Starting VILT Project (Laravel + Inertia + Vue + Tailwind)`n" -ForegroundColor Cyan

# Step 1: Start Docker containers
Write-Host "🔄 Starting Docker containers..." -ForegroundColor Yellow
docker compose up -d --build

# Step 2: Wait briefly to ensure containers are up
Start-Sleep -Seconds 5

# Step 3: Generate Laravel application key
Write-Host "🔑 Generating Laravel app key..." -ForegroundColor Yellow
docker exec -it vilt2_app php artisan key:generate

# Step 4: Run session table migration (if using database sessions)
Write-Host "📦 Creating session table..." -ForegroundColor Yellow
docker exec -it vilt2_app php artisan session:table

# Step 5: Run database migrations
Write-Host "🧱 Running migrations..." -ForegroundColor Yellow
docker exec -it vilt2_app php artisan migrate

# Step 6: Build frontend with Vite (optional)
Write-Host "🎨 Installing and building frontend assets..." -ForegroundColor Yellow
docker exec -it vilt2_node sh -c "npm install && npm run build"

# Final output
Write-Host "`n✅ Laravel app is running at: http://localhost:8010" -ForegroundColor Green
Write-Host "✅ Vite dev server (for frontend): http://localhost:5174`n" -ForegroundColor Green
