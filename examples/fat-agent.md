# Agent instructions

You are a careful senior Laravel engineer working in this repository.
Never invent HTTP endpoints, columns, or config keys.
If a fact is not in the repo, say so and stop.

The billing currency is XAF. Do not convert it.

## Policy

You must not invent columns, routes, or environment variables. Quote the file you used. If the schema and the prose disagree, the migration wins and you stop to ask. Keep answers short enough to paste into a pull request.

## Policy reminder

You must not invent columns, routes, or environment variables. Quote the file you used. If the schema and the prose disagree, the migration wins and you stop to ask. Keep answers short enough to paste into a pull request.

## Policy reminder, again

You must not invent columns, routes, or environment variables. Quote the file you used. If the schema and the prose disagree, the migration wins and you stop to ask. Keep answers short enough to paste into a pull request.

## Incident

The webhook worker died. Here is the trace someone pasted into the prompt:

#0 /var/www/html/app/Http/Controllers/OrderController.php(88): App\Billing\Invoice->charge()
#1 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Controller.php(54): OrderController->store()
#2 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/ControllerDispatcher.php(43): Controller->callAction()
#3 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Route.php(259): ControllerDispatcher->dispatch()
#4 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Router.php(800): Route->run()
#5 /var/www/html/vendor/laravel/framework/src/Illuminate/Pipeline/Pipeline.php(141): Router->Illuminate\Routing\{closure}()
#6 /var/www/html/vendor/laravel/framework/src/Illuminate/Routing/Middleware/SubstituteBindings.php(50): Pipeline->Illuminate\Pipeline\{closure}()
#7 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php(175): Pipeline->then()
#8 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/Http/Kernel.php(144): Kernel->sendRequestThroughRouter()
#9 /var/www/html/public/index.php(51): Kernel->handle()
#10 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php(16): require_once('/var/www/html/public/index.php')

## Payload someone dumped

This is synthetic filler, the letters QUJDREVG repeated. It is not a credential.
QUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVGQUJDREVG

data:image/gif;base64,R0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlhR0lGODlh

## Delivery log

2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo
2026-10-14T10:01:02Z INFO retrying webhook delivery id=wh_demo

## Notes with trailing space

Ship the invoice in XAF.    
Do not round to the nearest dollar.    



## Useful code that must survive a normal diet

```php
function charge(int $amount): bool
{
    return $amount > 0;
}
```
