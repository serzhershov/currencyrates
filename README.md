# Currencyrates
Touch ups on symphony, less legacy php and actual work with currency rates

## What I initially thought and wanted to achieve, before setting Symfony project up.

- set up symfony without any trouble
- connector and all the controllers api working in unison, using the same source as it is set in configuration
- building the connectors/getters with common interface, to achieve better maintanability for required future extensions
- connector factory, giving the right implementation of the connector depending on configuration
- a self validating data transfer object, so that any data related problems are discovered before database inserts are even attempted
- attempt to use MoneyPHP library for currency conversion and using it in the convert api

## What hindered me

- havign an outdated WM image of ubuntu 19.10, that required massive updates (took about 2-3 hrs to figure and iron out)
- having my past Symfony experience about 3 or 4 years ago, and general lack of recent symfony developement
- lack of xml parsing experience
- forgetting the correct way to do cross currency calculations (i'm still not sure that i implemented it totally accurate)

## What was eventually achieved

  Working command "app:rates-update"/src/Command/RatesUpdateCommand.php that invokes the correct connector depending on configuration of
"rates_connector_source" in config/services.yaml First intention was to have this parameter set up in .env files, but after some more 
pondering on tutorials i felt that the services.yaml is the correct place for such a configuration.

  Connectors for respective services, that are able to parse the current xml responses of the given resources, and return a common/expected
collection of data transfer objects with validation mar up attached. Xml parsing took it's sweet time, and honestly I gave in to stack overflow solution
for Ecb parser (it was durign the first night call to give up and go to sleep). In a better situation, i would have given more thought dividing the response
retrieval and response body parsing into separate services, with their own interfaces.

  An api controller that deals with rate converstion requests. Its not a fully restfull api by a mile, ust a quirky way to request conversion values that i 
grown to like, purely semanthycally. The controller itself has quite a few problems, as I feel it. First one - the input validation is far too cumbersome, and i'm
absolutely sure there is a nicer and cleaner way to achieve input validation. Currenry rate retireval from ORM and conversion, that should have been in its separate service for sure, and should have been a single line call in the api controller instead ot the whole flawed logic. I immensely failed with the conversion formulae, and even more with failing to try the MoneyPHP. Initially I was preparing the entities and the dto to work out with MoneyPHP.

  Absolutely lacking logging, this was intentianlly ignored during developement, to not overspend time on it and get to a functioning MVP.

## What changes i went trough during the weekend 1,5 days(or nights) of symfony
- Initial planning for 2 enitites - Currency, and CurrencyRates ended up with a single entity just for the rates, since i found a suitable package
with ISO currency names, symbols and all the required details
- deciding to cut logging all together, since symfony:serve functionality and debug packages give enough info to find most of the problems quickly.
- connector, dto and collection implementation went according to plan, and i'm mildly satisfied
- delayed trying MoneyPHP in favour of more time with purely Symfony 
- spent a ot more time reading than I expected
