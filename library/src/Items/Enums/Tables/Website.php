<?php
	/*
	Copyright (c) 2022 FenclWebDesign.com
	This script may not be copied, reproduced or altered in whole or in part.
	We check the Internet regularly for illegal copies of our scripts.
	Do not edit or copy this script for someone else, because you will be held responsible as well.
	This copyright shall be enforced to the full extent permitted by law.
	Licenses to use this script on a single website may be purchased from FenclWebDesign.com
	@Author: Developer
	*/
	
	namespace Items\Enums\Tables;
	
	use Items\Interfaces;
	use JetBrains\PhpStorm\Pure;
	
	enum Website: string implements Interfaces\TableEnum {
		case BLOGS                      = 'blogs';
		case CAREERS                    = 'careers';
		case CATEGORIES                 = 'categories';
		case EVENTS                     = 'events';
		case FAQS                       = 'faqs';
		case FORMS                      = 'forms';
		case GALLERIES                  = 'galleries';
		case GROUP_EVENTS               = 'group_events';
		case GROUPS                     = 'groups';
		case IMAGES                     = 'images';
		case MERCHANTS                  = 'merchants';
		case NEWS                       = 'news';
		case PAGES                      = 'pages';
		case PDFS                       = 'pdfs';
		case PODCAST_SUBSCRIPTION_TIERS = 'podcast_subscription_tiers';
		case PODCASTS                   = 'podcasts';
		case PRODUCTS                   = 'products';
		case ROUTES                     = 'routes';
		case SERVICES                   = 'services';
		case SLIDERS                    = 'sliders';
		case SPONSORS                   = 'sponsors';
		case STAFF                      = 'staff';
		case SUBSCRIPTIONS              = 'subscriptions';
		case TRANSACTIONS               = 'transactions';
		case USERS                      = 'users';
        case FRIDGE_SPACES = 'fridge_spaces';


        /**
		 * @param null|string $value
		 *
		 * @return null|$this
		 */
		public static function lookup(?string $value): ?self {
			return self::tryFrom($value);
		}
		
		/**
		 * @return string
		 */
		public function getValue(): string {
			return $this->value;
		}
		
		/**
		 * @return string
		 */
		public function getLabel(): string {
			return $this->name;
		}
		
		/**
		 * @param Interfaces\TableEnum ...$haystack
		 *
		 * @return bool
		 */
		#[Pure]
		public function is(Interfaces\TableEnum ...$haystack): bool {
			return in_array($this, $haystack);
		}
	}