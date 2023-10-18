import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { ItemsAtLocationComponent } from './items-at-location.component';

describe('ItemsAtLocationComponent', () => {
  let component: ItemsAtLocationComponent;
  let fixture: ComponentFixture<ItemsAtLocationComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ ItemsAtLocationComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(ItemsAtLocationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
