import {faMapMarked} from "@fortawesome/free-solid-svg-icons/faMapMarked";
import {faSearchLocation} from "@fortawesome/free-solid-svg-icons/faSearchLocation";
import {faClock} from "@fortawesome/free-solid-svg-icons/faClock";
import {faBox} from "@fortawesome/free-solid-svg-icons/faBox";
import {faCircleNotch} from "@fortawesome/free-solid-svg-icons/faCircleNotch";
import {faCircle, faHourglassHalf, faTruckLoading, faUndo} from "@fortawesome/free-solid-svg-icons";
import {faUsers} from "@fortawesome/free-solid-svg-icons/faUsers";
import {faWarehouse} from '@fortawesome/free-solid-svg-icons/faWarehouse';
import {faCheckCircle} from '@fortawesome/free-solid-svg-icons/faCheckCircle';

export const KPIS: { [id: string]: any; } = {
  customerSummary: {
    'label': 'Customer Summary',
    'link': '/dashboard/customer-summary',
    'icon': faUsers,
    'active': true,
    'description': ""
  },

  itemsAtLocation: {
    'label': 'Items at Site',
    'link': '/dashboard/items-at-site',
    'icon': faMapMarked,
    'active': true,
    'description': {
      atLocations: "Shows the total number of items at the selected Cintas location. Only items (lost and circulating) that are registered at the location at the moment are counted.",
      atCustomers: "Shows the total number of items per Cintas customer. Only items (lost and circulating) that are registered at the customer at the moment are counted.",
      perProduct: "Shows the total number of items (lost and circulating) per product and per site (i.e., at locations and at customers)."
    }
  },
  productLifetimeDelta: {
    'label': 'Product Lifetime Delta',
    'link': '/dashboard/product-lifetime-delta',
    'icon': faClock,
    'active': true,
    'description': "Shows the average lifetime delta of items per product (measured in cycle count).<br/> The lifetime delta in cycle count is computed as the difference between the expected lifetime of an item (as defined in the app for each product) and the actual lifetime when an item is marked as 'dismissed' in the app."
  },
  avgTurnaroundTime: {
    'label': 'Avg. Turnaround Time',
    'link': '/dashboard/avg-turnaround-time',
    'icon': faCircleNotch,
    'active': false,
    'description': "Shows the avg. number of item turnarounds per product in a period and the average time in days between delivery and return.<br/> The average turnaround time in days is computed by computing the difference between delivery and return over all deliveries and for all items and averaging the deltas per product."
  },

  containerTargetReach: {
    'label': 'Container Target Reach',
    'link': '/dashboard/container-target-reach',
    'icon': faBox,
    'active': false,
    'description': "Shows the total number of items packed on containers and the number of items missing compared to the container target defined for customers and products."
  },
  bundleRatio: {
    'label': 'Bundle Ratio in Outscans',
    'link': '/dashboard/bundle-ratio-outscans',
    'icon': faCheckCircle,
    'active': false,
    'description': "Shows the ratio of bundled to unbundled items registered during outscans over a period of time."
  },

  incomingOutgoingProducts: {
    'label': 'Incoming and Outgoing Products',
    'link': '/dashboard/incoming-outgoing-products',
    'icon': faWarehouse,
    'active': false,
    'description': 'Shows the total number of items and items per product type returned to the location (clean and soil returns) and delivered to customers.'
  },
  deliveredProducts: {
    'label': 'Proof Delivery',
    'link': '/dashboard/delivered-products',
    'icon': faTruckLoading,
    'active': false,
    'description': 'Shows the total number of items and items per product type delivered to customers in a given period.'
  },
  returnedProducts: {
    'label': 'Proof Return',
    'link': '/dashboard/returned-products',
    'icon': faUndo,
    'active': false,
    'description': 'Shows the total number of items per customer returned to the location (clean and soil returns).'
  },
};